<?php

namespace Invoize\Interfaces;

use Invoize\Models\Invoice\Payment\BasePayment;

abstract class ThirdPartyPayment extends BasePayment
{
    protected $checkout;

    public function getContent(): array
    {
        if (!$this->checkout) {
            throw new \Exception('Failed to create payment.');
        }
        return [
            'name' => $this->name,
            'method' => $this->method,
            'type' => $this->type,
            'checkout' => $this->checkout,
        ];
    }
}
