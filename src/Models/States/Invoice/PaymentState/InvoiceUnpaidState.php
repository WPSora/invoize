<?php

namespace Invoize\Models\States\Invoice\PaymentState;

use Invoize\Classes\Log;
use Invoize\Models\States\Invoice\PaymentState\BasePaymentState;
use Invoize\Models\Invoice;

class InvoiceUnpaidState extends BasePaymentState
{
    public function pay(bool $sendEmail = false)
    {
        $invoice = $this->invoice;
        $invoice->paymentStatus = Invoice::PAID;
        $invoice->updateTab(Invoice::PAID);
        $invoice->updatePaymentStatus(Invoice::PAID);
        $invoice->togglePaidDate(true);
        $invoice->createReceipt($invoice->ID);
        $invoice->updateSummary(Invoice::PAID, false);
        $invoice->saveActionHistory(Invoice::UNPAID, Invoice::PAID, 'mark this invoice as paid');
        $invoice->updateWcOrderToComplete($sendEmail);
        Log::action('Invoice changed to paid. ID: ' . $invoice->ID);
    }
}
