<?php

namespace Invoize\Models\States\Invoice\InvoiceState;

use Invoize\Models\States\Invoice\InvoiceState\BaseInvoiceState;
use Invoize\Models\Invoice;

class InvoiceActiveState extends BaseInvoiceState
{
    public function archive()
    {
        $inv = $this->invoice;
        $inv->updateInvoiceStatus(Invoice::ARCHIVED);
        $inv->updateTab(Invoice::ARCHIVED);
        $inv->updateReceiptStatus(Invoice::ARCHIVED);
        $inv->saveActionHistory($inv->getPaymentStatus(), Invoice::ARCHIVED, 'archived this invoice');
    }

    public function cancel()
    {
        $inv = $this->invoice;
        $inv->updateInvoiceStatus(Invoice::CANCELLED);
        $inv->updateTab(Invoice::CANCELLED);
        $inv->updateReceiptStatus(Invoice::CANCELLED);
        $inv->updateSummary();
        $inv->saveActionHistory($inv->getPaymentStatus(), Invoice::CANCELLED, 'cancelled this invoice');
    }

    public function trash()
    {
        $inv = $this->invoice;
        $paymentStatus = $inv->paymentStatus;
        $inv->updateInvoiceStatus(Invoice::TRASHED);
        $inv->updateTab(Invoice::TRASHED);
        $inv->updateReceiptStatus(Invoice::ARCHIVED);
        $inv->updateSummary();
        $inv->saveActionHistory($inv->getPaymentStatus(), Invoice::TRASHED, 'move this invoice to trash');
    }
}
