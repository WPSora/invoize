<?php

namespace Invoize\Models\States\Invoice\InvoiceState;

use Invoize\Classes\Log;
use Invoize\Models\Invoice;
use Invoize\Models\States\Invoice\InvoiceState\BaseInvoiceState;

class InvoiceExpiredState extends BaseInvoiceState
{
    // if invoice is expired, it means it ALWAYS Unpaid.
    // Paid invoice never become expired;
    public function pay()
    {
        $inv = $this->invoice;
        $inv->updateTab(Invoice::PAID);
        $inv->updateInvoiceStatus(Invoice::ACTIVE);
        $inv->updatePaymentStatus(Invoice::PAID);
        $inv->togglePaidDate(true);
        $inv->createReceipt($inv->ID);
        $inv->updateSummary(Invoice::PAID, false);
        $inv->saveActionHistory(Invoice::UNPAID, Invoice::PAID, 'mark this invoice as paid');
        $inv->updateWcOrder(false);
        Log::action('Invoice changed to paid. ID: ' . $inv->ID);
    }

    public function cancel()
    {
        $inv = $this->invoice;
        $inv->updateInvoiceStatus(Invoice::CANCELLED);
        $inv->updateTab(Invoice::CANCELLED);
        $inv->updateReceiptStatus(Invoice::CANCELLED);
        $inv->updateSummary($inv->paymentStatus, true);
        $inv->saveActionHistory($inv->paymentStatus, Invoice::CANCELLED, 'cancelled this invoice');
        Log::action('Invoice changed to cancelled. ID: ' . $inv->ID);
    }

    public function trash()
    {
        $inv = $this->invoice;
        $paymentStatus = $inv->paymentStatus;
        $inv->updateInvoiceStatus(Invoice::TRASHED);
        $inv->updateTab(Invoice::TRASHED);
        $inv->updateReceiptStatus(Invoice::ARCHIVED);
        $inv->updateSummary($paymentStatus, true);
        $inv->saveActionHistory($paymentStatus, Invoice::TRASHED, 'move this invoice to trash');
        Log::action('Invoice changed to trashed. ID: ' . $inv->ID);
    }
}
