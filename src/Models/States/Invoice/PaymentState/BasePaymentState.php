<?php

namespace Invoize\Models\States\Invoice\PaymentState;

use Invoize\Models\States\Interfaces\PaymentStateInterface;

abstract class BasePaymentState implements PaymentStateInterface
{
    protected $invoice;


    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }


    public function pay()
    {
        throw new \Exception('Invalid action');
    }


    public function unpay()
    {
        throw new \Exception('Invalid action');
    }
}
