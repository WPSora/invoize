<?php

if (! defined('ABSPATH')) exit; // Exit if accessed directly

use Invoize\Classes\Log;
use Invoize\Models\Setting;
use Invoize\Models\Invoice;
use Invoize\Models\Payment;
use Invoize\Models\States\Invoice\PaymentState\InvoicePaidState;
use Invoize\Models\States\Invoice\PaymentState\InvoiceUnpaidState;

global $invoizeGlobalParam;
invoizeValidateNonce($invoizeGlobalParam['nonce'] ?? null);

// This will be your Webhook Verification Token you can obtain from the dashboard.
// Make sure to keep this confidential and not to reveal to anyone.
// This token will be used to verify the origin of request validity is really from Xendit
try {
    $xenditXCallbackToken = Setting::getXenditToken();
} catch (Exception $e) {
    Log::error('Xendit webhook failed: Verification failed. Missing Xendit token.');
    echo esc_html('Verification failed. Missing Xendit token');
    http_response_code(422);
    return;
}

// This section is to get the webhook Token from the header request, 
// which will then later to be compared with our xendit webhook verification token
$reqHeaders = getallheaders();
$xIncomingCallbackTokenHeader = isset($reqHeaders['X-Callback-Token']) ? $reqHeaders['X-Callback-Token'] : "";
// In order to ensure the request is coming from xendit
// You must compare the incoming token is equal with your xendit webhook verification token
// This is to ensure the request is coming from Xendit and not from any other third party.
if ($xIncomingCallbackTokenHeader === $xenditXCallbackToken) {
    // Incoming Request is verified coming from Xendit
    // You can then perform your checking and do the necessary, 
    // such as update your invoice records

    // This line is to obtain all request input in a raw text json format
    $rawRequestInput = file_get_contents("php://input");
    // This line is to format the raw input into associative array
    $arrRequestInput = json_decode($rawRequestInput, true);

    $id     = isset($arrRequestInput['external_id']) ? sanitize_text_field($arrRequestInput['external_id']) : '';
    $status = isset($arrRequestInput['status']) ? sanitize_text_field($arrRequestInput['status']) : '';

    try {
        $invoice = Invoice::whereHas('metas', function ($metas) use ($id) {
            $metas
                ->where('meta_key', 'id')
                ->where('meta_value', $id);
        })->first();

        if (!$invoice) {
            Log::error('Xendit webhook failed: Invoice not found');
            echo esc_html('Invoice not found. But if you are testing the webhook, it means Invoize webhook successfully connected.');
            http_response_code(404);
            return;
        }

        $paymentState = $invoice->getPaymentState();

        if ($paymentState instanceof InvoiceUnpaidState && $status === "PAID") {
            $paymentState->pay(false, Payment::XENDIT);
            Invoice::sendMail($invoice->ID, Invoice::PAID);
        } else if ($paymentState instanceof InvoicePaidState) {
            Log::error('Xendit webhook failed: Invoice is already paid');
            echo esc_html('Invoice is already paid');
        }
    } catch (Exception $e) {
        Log::error('Xendit webhook failed: Invoice payment status not updated. ID: ' . $id . '. Message: ' . $e->getMessage());
        echo esc_html('Failed to update invoice payment status. Please check Invoize log.');
    }
} else {
    // Request is not from xendit, reject and throw http status forbidden
    Log::error('Xendit webhook failed: Verification failed. Wrong token');
    echo esc_html('Verification failed. Wrong token.');
    http_response_code(403);
}
