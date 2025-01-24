<?php

namespace Invoize\Models\States\Invoice\InvoiceState;

use Invoize\Models\States\Invoice\InvoiceState\BaseInvoiceState;
use Invoize\Models\Invoice;

class InvoiceCancelledState extends BaseInvoiceState
{
    public function activate()
    {
        $inv = $this->invoice;
        $status = $inv->paymentStatus;
        $inv->updateInvoiceStatus(Invoice::ACTIVE);
        $inv->updateTab($status);
        $inv->updateReceiptStatus(Invoice::PAID);
        $inv->updateSummary($status, true, false);
        $inv->saveActionHistory(Invoice::CANCELLED, $status, 'restore this invoice from cancelled');
    }

    public function trash()
    {
        $inv = $this->invoice;
        $inv->updateInvoiceStatus(Invoice::TRASHED);
        $inv->updateTab(Invoice::TRASHED);
        $inv->updateReceiptStatus(Invoice::ARCHIVED);
        $inv->saveActionHistory(Invoice::CANCELLED, Invoice::TRASHED, 'move this invoice to trash');
    }
}
