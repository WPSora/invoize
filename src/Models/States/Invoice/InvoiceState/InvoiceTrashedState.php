<?php

namespace Invoize\Models\States\Invoice\InvoiceState;

use Invoize\Classes\Log;
use Invoize\Models\States\Invoice\InvoiceState\BaseInvoiceState;
use Invoize\Models\Invoice;

class InvoiceTrashedState extends BaseInvoiceState
{
    public function activate()
    {
        $inv = $this->invoice;
        $status = $inv->paymentStatus;
        $inv->updateInvoiceStatus(Invoice::ACTIVE);
        $inv->updateTab($status);
        $inv->updateReceiptStatus(Invoice::PAID);
        $inv->updateSummary($status, true, false);
        $inv->saveActionHistory(Invoice::TRASHED, $status, 'restore this invoice from trash');
        Log::action('Invoice restored from trash. ID: ' . $inv->ID);
    }
}
