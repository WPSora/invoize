<?php

namespace Invoize\Models\States\Invoice\InvoiceState;

use Invoize\Models\States\Interfaces\InvoiceStateInterface;

abstract class BaseInvoiceState implements InvoiceStateInterface
{
    protected $invoice;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    public function activate()
    {
        throw new \Exception('Invalid action');
    }

    public function archive()
    {
        throw new \Exception('Invalid action');
    }

    public function cancel()
    {
        throw new \Exception('Invalid action');
    }

    public function trash()
    {
        throw new \Exception('Invalid action');
    }
}
