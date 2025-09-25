<?php

namespace Invoize\Api\Payments;

use Exception;
use Invoize\Api\Api;
use Invoize\Classes\Log;
use Invoize\Models\Invoice;
use Invoize\Models\Payment;
use Invoize\Models\Setting;
use Invoize\Payments\Paypal\PaypalCheckout;

class PaymentAPI extends Api
{
    protected string $routeName = 'payment';

    public function pay($request)
    {
        $token      = sanitize_text_field($request->get_param('token'));
        $payment    = sanitize_text_field($request->get_param('payment'));

        if (!$payment || !in_array($payment, [Payment::XENDIT, Payment::PAYPAL_AUTO_CONFIRMATION])) {
            return $this->response([
                'message' => "Invalid payment"
            ], 422);
        }

        $invoiceObj = Invoice::findByToken($token);
        $invoice    = $invoiceObj->detail();

        $defaultData = [
            'id'        => $invoice['id'],
            'token'     => $invoice['token'],

        ];
        $data = apply_filters(
            'invoize_payment_payload',
            [
                'total'             => $invoice['total'],
                'due_date'          => $invoice['dueDate'],
                'convertedTotal'    => 0 // Only for Xendit
            ],
            $invoice
        );

        $data = array_merge($defaultData, $data);

        if ($invoice['paymentStatus'] === Invoice::PAID) {
            return $this->response([
                'message' => "Invoice is already paid"
            ], 403);
        }

        if ($payment == Payment::XENDIT) {

            $xenditDetail = array_filter($invoice['payments'], function ($data) {
                return $data['method'] == Payment::XENDIT;
            });

            $xenditDetail = reset($xenditDetail);
            if (
                isset($xenditDetail['checkout']['status']) &&
                $xenditDetail['checkout']['status'] == 'PENDING' &&
                isset($xenditDetail['checkout']['invoice_url'])
            ) {
                Log::action('Returning existing Xendit payment url: ' . $xenditDetail['checkout']['invoice_url'] ?? '');
                return $this->response([
                    'link' => $xenditDetail['checkout']['invoice_url']
                ]);
            }

            if (isset($xenditDetail['checkout']['invoice_url'])) {
                Log::action('Returning existing Xendit payment url: ' . $xenditDetail['checkout']['invoice_url'] ?? '');
                return $this->response([
                    'link' => $xenditDetail['checkout']['invoice_url']
                ]);
            }

            $convertedTotal = $data['total'];

            if (!$data['convertedTotal']) {

                if (!in_array($invoice['currency']['name'], Payment::XENDIT_SUPPORTED_CURRENCIES)) {
                    Log::error('Xendit payment error. Currency are not supported');
                    return $this->response([
                        'message' => "Currency Aren't supported, XENDIT only support " . implode(',', Payment::XENDIT_SUPPORTED_CURRENCIES)
                    ], 422);
                }

                $primaryCurrency = Setting::key('payment.xenditPrimaryCurrency')
                    ->value('option_value');

                if ($primaryCurrency != $invoice['currency']['name']) {
                    $convertedMultiplier = Setting::key(
                        'payment.xenditCurrencyConverter_' . $invoice['currency']['name']
                    )->value('option_value');

                    if (!$convertedMultiplier) {
                        Log::error('Xendit payment error. Currency converter has not been set up yet.');
                        return $this->response([
                            'message' => "The Currency Converter has not been set up yet."
                        ], 422);
                    }

                    $convertedTotal = (float) $convertedMultiplier * $invoice['total'];
                }
            }

            if ($data['convertedTotal']) {
                $convertedTotal = $data['convertedTotal'];
            }
            Log::action('Creating Xendit payment from PaymentAPI. ID:' . $invoice['id']);

            try {
                $xendit = Payment::createXenditPayment__premium_only(
                    $invoice['invoiceNumber'] ?? $invoice['id'],
                    $invoice['token'],
                    $convertedTotal,
                    $data['due_date'],
                    $invoice['client']
                );
            } catch (Exception $e) {
                return $this->response(['message' => $e->getMessage()], 500);
            }

            if (!$xenditDetail) {
                $invoice['payments'][] = [
                    'method' => Payment::XENDIT,
                    'name' => Payment::XENDIT,
                    'checkout' => $xendit
                ];
            } else {
                $invoice['payments'] = array_map(function ($record) use ($xendit) {
                    if ($record['method'] == Payment::XENDIT) {
                        $record['checkout'] = $xendit;
                    }
                    return $record;
                }, $invoice['payments']);
            }

            // Update the meta
            $invoiceObj->updateMeta(['payments' => $invoice['payments']]);


            return $this->response(['link' => $xendit['invoice_url']]);
        }


        if ($payment == Payment::PAYPAL_AUTO_CONFIRMATION) {
            $paypalDetail = array_filter($invoice['payments'], function ($data) {
                return $data['method'] == Payment::PAYPAL && $data['type'] == Payment::AUTO_CONFIRMATION;
            });
            $paypalDetail = reset($paypalDetail);

            if (
                isset($paypalDetail['checkout']['status']) &&
                $paypalDetail['checkout']['status'] == PaypalCheckout::PAYER_ACTION_REQUIRED &&
                isset($paypalDetail['checkout']['links'])
            ) {
                $paypalLink = array_filter($paypalDetail['checkout']['links'], function ($link) {
                    return $link['rel'] === 'payer-action';
                });
                $paypalLink = reset($paypalLink);

                if (isset($paypalLink['href'])) {
                    Log::action('Returning existing Paypal payment url: ' . $paypalLink['href']);
                    return $this->response([
                        'link' => $paypalLink['href']
                    ]);
                }
            }

            if (isset($paypalDetail['checkout']['links'])) {
                $paypalLink = array_filter($paypalDetail['checkout']['links'], function ($link) {
                    return $link['rel'] === 'payer-action';
                });
                $paypalLink = reset($paypalLink);

                if (isset($paypalLink['href'])) {
                    Log::action('Returning existing Paypal payment url: ' . $paypalLink['href']);
                    return $this->response([
                        'link' => $paypalLink['href']
                    ]);
                }
            }

            Log::action('Creating Paypal payment from PaymentAPI. ID:' . $invoice['id']);

            try {
                $checkout = Payment::createPaypalPayment__premium_only(
                    $invoice['invoiceNumber'] ?? $invoice['id'],
                    $invoice['token'],
                    $invoice['currency']['name'],
                    $invoice['total'],
                );
            } catch (Exception $e) {
                return $this->response(['message' => $e->getMessage()], 500);
            }

            if (!$paypalDetail) {
                $invoice['payments'][] = [
                    'name'     => Payment::PAYPAL,
                    'method'   => Payment::PAYPAL,
                    'type'     => Payment::AUTO_CONFIRMATION,
                    'checkout' => $checkout,
                ];
            } else {
                $invoice['payments'] = array_map(function ($record) use ($checkout) {
                    if ($record['method'] == Payment::PAYPAL && $record['type'] == Payment::AUTO_CONFIRMATION) {
                        $record['checkout'] = $checkout;
                    }
                    return $record;
                }, $invoice['payments']);
            }

            $invoiceObj->updateMeta(['payments' => $invoice['payments']]);

            $link = array_filter($checkout['links'], fn($link) => $link['rel'] == 'payer-action');
            $link = reset($link);

            return $this->response(['link' => $link['href']]);
        }
    }

    public function registerRoutes()
    {
        $this->registerPostRequest('pay', [
            'callback' => [$this, 'pay'],
            'permission_callback' => function () {
                return true;
            }
        ]);
    }
}
