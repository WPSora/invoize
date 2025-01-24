<?php

namespace Invoize\Api\Download;

use Invoize\Api\Api;
use Invoize\Models\Invoice;
use WP_REST_Request;
use Dompdf\Adapter\CPDF;


class DownloadAPI extends Api
{
    protected string $routeName = 'download';

    public function downloadInvoice(WP_REST_Request $request)
    {
        $token = sanitize_text_field($request->get_param('token'));
        $paper = sanitize_text_field($request->get_param('paper'));

        if (!$token) {
            return $this->response(['message' => 'Missing id parameter'], 422);
        }

        $inv = Invoice::findByToken($token);

        if (!$inv) {
            return $this->response(['message' => 'Invoice not found'], 404);
        }

        return $this->response([
            'filename' => "{$inv->getInvoiceNumber()} - {$inv->getClient()['name']}.pdf",
            'content' => base64_encode($inv->getPdf())
        ]);
    }

    public function downloadReceipt(WP_REST_Request $request)
    {
        $token = sanitize_text_field($request->get_param('token'));
        $paper = sanitize_text_field($request->get_param('paper'));

        if (!$token) {
            return $this->response(['message' => 'Missing id parameter'], 422);
        }

        $inv = Invoice::findByToken($token);

        if (!$inv) {
            return $this->response(['message' => 'Invoice not found'], 404);
        }

        if ($inv->isPaymentStatusUnpaid()) {
            return $this->response(['message' => 'Receipt not found'], 404);
        }
        return $this->response([
            'filename' => "{$inv->receipt->getReceiptNumber()} - {$inv->getClient()['name']}.pdf",
            'content' => base64_encode($inv->receipt->getPdf())
        ]);
    }

    public function registerRoutes()
    {
        $this->registerGetRequest('invoice', [
            'callback' => [$this, 'downloadInvoice'],
        ]);

        $this->registerGetRequest('receipt', [
            'callback' => [$this, 'downloadReceipt'],
        ]);
    }
}
