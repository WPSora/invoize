<?php

namespace Invoize\Models\Invoice\Payment\Woocommerce;

use Invoize\Models\Invoice\Payment\BasePayment;
use Invoize\Models\Payment;

class WoocommercePayment extends BasePayment
{
    private $detail; // string | array
    private $checkout;

    public function __construct()
    {
        $this->method = Payment::WOOCOMMERCE_TRANSACTION;
    }

    public function setContent(array $payment): self
    {
        switch ($payment['type']) {
            case Payment::WOOCOMMERCE_BANK:
                $this->setBank($payment);
                break;
            case Payment::WOOCOMMERCE_PAYPAL:
                $this->setPaypal($payment);
                break;
        }
        return $this;
    }

    private function setPaypal(array $paypal)
    {
        $this->type = Payment::WOOCOMMERCE_PAYPAL;
        $this->name = Payment::WOOCOMMERCE_PAYPAL;
        $this->detail = $paypal['detail'];
    }

    private function setBank(array $bank)
    {
        $this->type = Payment::WOOCOMMERCE_BANK;
        $this->name = $bank['name'];
        $this->detail = $bank['detail'];
    }

    public function getContent(): array
    {
        return [
            'name' => $this->name,
            'method' => $this->method,
            'type' => $this->type,
            'detail' => $this->detail,
            'checkout' => $this->checkout,
        ];
    }
}
