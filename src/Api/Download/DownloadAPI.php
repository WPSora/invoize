<?php

namespace Invoize\Api\Download;

use Invoize\Api\Api;
use Invoize\Models\Invoice;
use WP_REST_Request;
use Dompdf\Adapter\CPDF;
use Invoize\Models\Quotation;

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

    public function downloadQuotation(WP_REST_Request $request)
    {
        $token = sanitize_text_field($request->get_param('token'));
        if (!$token) {
            return $this->response(['message' => 'Missing id parameter'], 422);
        }

        $quotation = Quotation::findByToken($token);

        if (!$quotation) {
            return $this->response(['message' => 'Quotation not found'], 404);
        }

        return $this->response([
            'filename' => "{$quotation->getQuotationNumber()} - {$quotation->getClient()['name']}.pdf",
            'content' => base64_encode($quotation->getPdf())
        ]);
    }

    public function registerRoutes()
    {
        $this->registerGetRequest('invoice', [
            'callback' => [$this, 'downloadInvoice'],
            'permission_callback' => function (WP_REST_Request $request) {
                return invoizeCheckPermissionIsAllowed() || current_user_can('invoize_customer') || current_user_can('customer');
            }
        ]);

        $this->registerGetRequest('quotation', [
            'callback' => [$this, 'downloadQuotation'],
            'permission_callback' => function (WP_REST_Request $request) {
                return invoizeCheckPermissionIsAllowed() || current_user_can('invoize_customer') || current_user_can('customer');
            }
        ]);

        $this->registerGetRequest('receipt', [
            'callback' => [$this, 'downloadReceipt'],
            'permission_callback' => function (WP_REST_Request $request) {
                return invoizeCheckPermissionIsAllowed() || current_user_can('invoize_customer') || current_user_can('customer');
            }
        ]);
    }
}
