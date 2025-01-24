<?php

namespace Invoize\Models\States\Invoice\InvoiceState;

use Invoize\Models\States\Invoice\InvoiceState\BaseInvoiceState;
use Invoize\Models\Invoice;

class InvoiceArchivedState extends BaseInvoiceState
{
    public function activate()
    {
        $inv = $this->invoice;
        $inv->updateInvoiceStatus(Invoice::ACTIVE);
        $inv->updateTab($inv->paymentStatus);
        $inv->updateReceiptStatus(Invoice::PAID);
        $inv->saveActionHistory(Invoice::ARCHIVED, $inv->paymentStatus, 'unarchived this invoice');
    }

    public function cancel()
    {
        $inv = $this->invoice;
        $inv->updateInvoiceStatus(Invoice::CANCELLED);
        $inv->updateTab(Invoice::CANCELLED);
        $inv->updateReceiptStatus(Invoice::CANCELLED);
        $inv->updateSummary($inv->paymentStatus, true);
        $inv->saveActionHistory(Invoice::ARCHIVED, Invoice::CANCELLED, 'cancelled this invoice');
    }

    public function trash()
    {
        $inv = $this->invoice;
        $inv->updateInvoiceStatus(Invoice::TRASHED);
        $inv->updateTab(Invoice::TRASHED);
        $inv->updateReceiptStatus(Invoice::ARCHIVED);
        $inv->updateSummary($inv->paymentStatus, true);
        $inv->saveActionHistory(Invoice::ARCHIVED, Invoice::TRASHED, 'move this invoice to trash');
    }
}
