<?php

namespace Invoize\Api\Payments;

use Invoize\Api\Api;
use Invoize\Classes\Log;
use Invoize\InvoizePlugin;
use Invoize\Models\Invoice;
use Invoize\Models\Payment;
use Invoize\Models\Setting;
use Invoize\Payments\Paypal\PaypalCheckout;
use WP_REST_Request;

class PaypalAPI extends Api
{
    protected string $routeName = 'paypal';

    public function capture(WP_REST_Request $request)
    {
        $orderToken = $request->get_param('token');
        $invoiceId = $request->get_param('invoiceId');

        if (!$orderToken) {
            return wp_send_json_error([
                'message' => 'token is empty'
            ], 400);
        }

        $invoice = Invoice::find($invoiceId);

        if (!$invoice) {
            return $this->response(['message' => 'Invoice not found'], 404);
        }

        try {
            $captureCheckout = PaypalCheckout::init($orderToken)->capture();
            $invoice->updatePaidMethod(Payment::PAYPAL_AUTO_CONFIRMATION);
        } catch (\Exception $e) {
            return wp_send_json_error([
                'message' => $e->getMessage()
            ], 500);
        }

        try {
            Invoice::sendMail($invoiceId, Invoice::PAID);
        } catch (\Exception $e) {
            Log::emailError("InvoiceID: $invoiceId. Message: " . $e->getMessage());
        }

        return wp_send_json_success($captureCheckout);
    }


    public function authenticate(WP_REST_Request $request)
    {
        $plugin = InvoizePlugin::getInstance()->getSlug();
        $params = $request->get_params();
        $sandbox = [];
        $live = [];

        foreach ($params as $paypal) {
            if (filter_var($paypal['isSandbox'], FILTER_VALIDATE_BOOL)) {
                $data = [];
                foreach ($paypal as $key => $value) {
                    $data[$key] = sanitize_text_field($value);
                }
                $sandbox[] = $data;
            } else {
                $data = [];
                foreach ($paypal as $key => $value) {
                    $data[$key] = sanitize_text_field($value);
                }
                $live[] = $data;
            }
        }

        if (!empty($sandbox) && isset($sandbox[0]) && isset($sandbox[0]['clientId']) && isset($sandbox[0]['secretId'])) {
            try {
                $sandboxResult = PaypalCheckout::validateKey(true, $sandbox[0]['clientId'], $sandbox[0]['secretId']);
                $sandboxSetting = $plugin . '.payment.paypal.token.sandbox';
                $sandboxResult['expired_at'] = time() + $sandboxResult['expires_in'];
                get_option($sandboxSetting)
                    ? update_option($sandboxSetting, serialize($sandboxResult))
                    : add_option($sandboxSetting, serialize($sandboxResult));
            } catch (\Exception $e) {
                Log::error('Failed to authenticate Paypal payment (Sandbox account). ' . $e->getMessage());
                return wp_send_json_error(
                    $e->getMessage() . ' (Sandbox Account)',
                    $e->getCode(),
                );
            }
        }
        if (!empty($live)  && isset($live[0]) && isset($live[0]['clientId']) && isset($live[0]['secretId'])) {
            try {
                $liveResult = PaypalCheckout::validateKey(false, $live[0]['clientId'], $live[0]['secretId']);
                $liveSetting = $plugin . '.payment.paypal.token.live';
                $liveResult['expired_at'] = time() + $liveSetting['expires_in'];
                get_option($liveSetting)
                    ? update_option($liveSetting, serialize($liveResult))
                    : add_option($liveSetting, serialize($liveResult));
            } catch (\Exception $e) {
                Log::error('Failed to authenticate Paypal payment (Live account).' . $e->getMessage());
                return wp_send_json_error(
                    $e->getMessage() . ' (Live Account)',
                    $e->getCode(),
                );
            }
        }
        return wp_send_json_success();
    }


    public function deleteToken(WP_REST_Request $request)
    {
        $param = $request->get_param('isSandbox');
        if (!isset($param)) {
            return $this->response(['message' => 'Missing parameter'], 422);
        }
        $isSandbox = filter_var($param, FILTER_VALIDATE_BOOL);
        $isSandbox
            ? Setting::key('payment.paypal.token.sandbox')->delete()
            : Setting::key('payment.paypal.token.live')->delete();

        return $this->response(['message' => 'Token deleted']);
    }


    public function registerRoutes()
    {
        $this->registerPostRequest('capture', [
            'callback' => [$this, 'capture']
        ]);

        $this->registerPostRequest('authenticate', [
            'callback' => [$this, 'authenticate']
        ]);

        $this->registerPostRequest('delete-token', [
            'callback' => [$this, 'deleteToken']
        ]);
    }
}
