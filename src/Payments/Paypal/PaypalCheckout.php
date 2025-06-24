<?php

namespace Invoize\Payments\Paypal;

use Exception;
use Invoize\Classes\Log;
use Invoize\InvoizePlugin;
use Invoize\Models\Setting;
use Invoize\Payments\Checkout;
use Invoize\Payments\Constant\PaymentError;

// '{"scope":"https://uri.paypal.com/services/invoicing https://uri.paypal.com/services/disputes/read-buyer https://uri.paypal.com/services/payments/realtimepayment https://uri.paypal.com/services/disputes/update-seller https://uri.paypal.com/services/payments/payment/authcapture openid https://uri.paypal.com/services/disputes/read-seller https://uri.paypal.com/services/payments/refund https://api.paypal.com/v1/vault/credit-card https://api.paypal.com/v1/payments/.* https://uri.paypal.com/payments/payouts https://api.paypal.com/v1/vault/credit-card/.* https://uri.paypal.com/services/subscriptions https://uri.paypal.com/services/applications/webhooks","access_token":"A21AALljxXOtA2SDJ-Dqb7xjhVmTs27fT6YRRkTjn3Wt_C3JhBXflrcheggDr0P07HHfJZcTL-eXbQ6h8P6RUdWErJiWd3e1A","token_type":"Bearer","app_id":"APP-80W284485P519543T","expires_in":32400,"nonce":"2024-01-21T12:52:50Z9I6HYxK7jxqBQ3f8M4qE6rqTNaz7HW7Cf7NegNQ7-nA"}'

/**
 * 1. How long access token is valid? 32400
 * 2. How long link checkout is valid ? 3 hours
 * 3. Is Credit Card Allowed ?
 */


// https://developer.paypal.com/docs/api/orders/v2/#orders_create
class PaypalCheckout extends Checkout
{
    public const INTENT_CAPTURE = 'CAPTURE';
    public const USER_ACTION_PAY_NOW = 'PAY_NOW';
    public const SANDBOX_API_URL = "https://api-m.sandbox.paypal.com";
    public const LIVE_API_URL = "https://api-m.paypal.com";

    protected string $clientId;
    protected string $clientSecret;
    protected string $accessToken;
    protected array $headers = [];
    protected array $items = [];
    protected float $total = 0;
    protected string $currency = 'USD';
    protected bool $isSandboxMode = true;
    protected array $paymentSource = [];
    // https://developer.paypal.com/reference/currency-codes/
    public const CURRENCIES = [
        'AUD',
        'BRL',
        'CAD',
        'CNY',
        'CZK',
        'DKK',
        'EUR',
        'HKD',
        'HUF',
        'ILS',
        'JPY',
        'MYR',
        'MXN',
        'TWD',
        'MYR',
        'NZD',
        'NOK',
        'PHP',
        'PLN',
        'GBP',
        'SGD',
        'SEK',
        'CHF',
        'THB',
        'USD'
    ];


    public function __construct(?string $orderId = null, ?string $invoiceToken = null)
    {
        parent::__construct($orderId, $invoiceToken);

        $options = Setting::key('payment.automaticPaypals')->value('option_value');
        $sandboxOption = Setting::key('payment.activeAutomaticPaypalType')->value('option_value');
        if (!$options || !$sandboxOption) {
            Log::error('Missing paypal settings');
            throw new \Exception('Missing paypal settings', 422);
        }

        $this->isSandboxMode = $sandboxOption == 'sandbox';
        $check = $this->isSandboxMode ? "true" : "false";
        $result = array_values(array_filter(unserialize($options), function ($item) use ($check) {
            return $item['isSandbox'] === $check;
        }))[0] ?? null;

        if (!isset($result)) {
            Log::error('Missing paypal settings');
            throw new \Exception('Missing paypal settings', 422);
        }
        $this->clientId = $result['clientId'];
        $this->clientSecret = $result['secretId'];
        $this->refreshAccessToken();
    }


    public function apiUrl(string $route)
    {
        return ($this->isSandboxMode
            ? static::SANDBOX_API_URL
            : static::LIVE_API_URL) . '/v2' . $route;
    }


    public function currency(string $currency)
    {
        if (!in_array($currency, static::CURRENCIES)) {
            throw new \Exception(esc_html(PaymentError::PAYPAL_UNSUPPORTED_CURRENCY), 400);
        }
        $this->currency = $currency;
        return $this;
    }


    public function getCurrency(): string
    {
        // If currency is not set, use USD as default
        if (!$this->currency) {
            return "USD";
        }
        return $this->currency;
    }


    public function total(float $amount)
    {
        $this->total = $amount;
        return $this;
    }


    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }


    public function setPaymentSource(array $paymentSource)
    {
        $this->paymentSource = $paymentSource;
        return $this;
    }


    public function getHeaders()
    {
        return $this->headers;
    }


    public function refreshAccessToken()
    {
        if (!$this->clientId || !$this->clientSecret) {
            throw new \Exception('Client ID or Secret ID is empty', 422);
        }
        $settingName = $this->isSandboxMode
            ? InvoizePlugin::getInstance()->getSlug() . '.payment.paypal.token.sandbox'
            : InvoizePlugin::getInstance()->getSlug() . '.payment.paypal.token.live';
        $token = unserialize(get_option($settingName));
        if ($token && $token['expired_at'] > time()) {
            $this->accessToken = $token['access_token'];
            return;
        }

        $result = static::validateKey(
            $this->isSandboxMode,
            $this->clientId,
            $this->clientSecret
        );

        $result['expired_at'] = time() + $result['expires_in'];
        $this->accessToken = $result['access_token'];
        get_option($settingName)
            ? update_option($settingName, serialize($result))
            : add_option($settingName, serialize($result));
    }


    public static function validateKey($isSandbox, $clientId, $secretId): array
    {
        $auth = 'Basic ' . base64_encode("$clientId:$secretId");
        $result = wp_remote_post(
            $isSandbox ?
                static::SANDBOX_API_URL . '/v1/oauth2/token' :
                static::LIVE_API_URL . '/v1/oauth2/token',
            [
                'headers' => [
                    'Authorization' => $auth,
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'body' => [
                    'grant_type' => 'client_credentials'
                ]
            ]
        );
        if (is_wp_error($result)) {
            Log::error('Failed to get Paypal access token. ' . $result->get_error_message());
            throw new \Exception(
                esc_html("Failed to get Paypal access token {$result->get_error_message()}"),
                500
            );
        }
        $result = json_decode(wp_remote_retrieve_body($result), true);
        if (!isset($result['access_token'])) {
            Log::error('Invalid paypal key');
            throw new \Exception('INVALID_PAYPAL_KEY', 422);
        }
        return $result;
    }


    public function capture()
    {
        if (!$this->orderId) {
            throw new \Exception('Order ID is empty', 422);
        }

        Log::action('Capturing paypal payment. OrderId:' . $this->orderId);

        $result = wp_remote_post(
            $this->apiUrl("/checkout/orders/{$this->orderId}/capture"),
            [
                "headers" => [
                    "Authorization" => "Bearer {$this->accessToken}",
                    "Content-Type" => "application/json"
                ],
                "body" => wp_json_encode([
                    "intent" => static::INTENT_CAPTURE,
                ])
            ]
        );


        $body = wp_remote_retrieve_body($result);
        $bodyDecode = json_decode($body, true);
        $code = wp_remote_retrieve_response_code($result);
        $message = wp_remote_retrieve_response_message($result);

        if ($code != 200 || $code != 201) {
            $errorName = isset($bodyDecode['name']) ? $bodyDecode['name'] : $message;
            Log::error('Failed to capture Paypal payment. ' . '. ' . $errorName . '. ' . $message . '. ' . $body);
            throw new \Exception(esc_html("$errorName: $body", $code));
        }

        return $bodyDecode;
    }


    public function create()
    {
        $result = wp_remote_post(
            $this->apiUrl("/checkout/orders"),
            [
                "headers" => [
                    "Authorization" => "Bearer {$this->accessToken}",
                    "Content-Type" => "application/json"
                ],
                "body" => wp_json_encode([
                    "intent" => static::INTENT_CAPTURE,
                    "payment_source" => [
                        "paypal" => [
                            "experience_context" => [
                                // 'brand_name' => "Open Journal Team", // get from option
                                'user_action' => static::USER_ACTION_PAY_NOW,
                                'return_url' => home_url(
                                    'invoize-preview/' . $this->invoiceToken
                                ),
                            ]
                        ]
                    ],
                    "purchase_units" => [[
                        'amount' => [
                            'currency_code' => $this->getCurrency(),
                            'value' => $this->total,
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => $this->getCurrency(),
                                    'value' => $this->total,
                                ],
                            ],
                        ],
                        'items' => [
                            [
                                'name' => "Payment for invoice {$this->orderId}",
                                'quantity' => 1,
                                'unit_amount' => [
                                    'currency_code' => $this->getCurrency(),
                                    'value' => $this->total,
                                ],
                            ]
                        ],
                    ]]
                ])
            ]
        );

        $body = wp_remote_retrieve_body($result);
        $bodyDecode = json_decode($body, true);
        $code = wp_remote_retrieve_response_code($result);
        $message = wp_remote_retrieve_response_message($result);

        if ($code != 200) {
            $plugin = InvoizePlugin::getInstance()->getPluginName();
            $errorName = isset($bodyDecode['name']) ? $bodyDecode['name'] : $message;
            Log::error('Paypal payment failed. ' . '. ' . $errorName . '. ' . $message . '. ' . $body);
            throw new \Exception(esc_html("$errorName: $body"), esc_html($code));
        }

        Log::action('Paypal payment is created.');

        return $bodyDecode;
    }
}
