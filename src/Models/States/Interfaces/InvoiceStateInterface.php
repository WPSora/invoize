<?php

namespace Invoize\Models\States\Interfaces;

interface InvoiceStateInterface
{
    public function activate();

    public function archive();

    public function trash();

    public function cancel();
}
