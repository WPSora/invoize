<?php

namespace Invoize\Models\Invoice\Payment;

use Invoize\Interfaces\InvoiceContentInterface;

abstract class BasePayment implements InvoiceContentInterface
{
    protected string $name = '';
    protected string $type = '';
    protected string $method;
    protected ?PaymentParameter $parameter;

    public static function instance()
    {
        return new static;
    }

    public function setParameter(PaymentParameter $param)
    {
        $this->parameter = $param;
        return $this;
    }
}
