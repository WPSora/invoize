<?php

namespace Invoize\Models\States\Invoice\PaymentState;

use Invoize\Classes\Log;
use Invoize\Models\States\Invoice\PaymentState\BasePaymentState;
use Invoize\Models\Invoice;

class InvoicePaidState extends BasePaymentState
{
    public function unpay()
    {
        $invoice = $this->invoice;
        $invoice->paymentStatus = Invoice::UNPAID;
        $invoice->updateTab(Invoice::UNPAID);
        $invoice->updatePaymentStatus(Invoice::UNPAID);
        $invoice->togglePaidDate(false);
        $invoice->deleteReceipt();
        $invoice->updateSummary(Invoice::UNPAID, false);
        $invoice->saveActionHistory(Invoice::PAID, Invoice::UNPAID, 'mark this invoice as unpaid');
        Log::action('Invoice changed to unpaid. ID: ' . $invoice->ID);
    }
}
