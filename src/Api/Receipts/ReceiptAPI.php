<?php

namespace Invoize\Api\Receipts;

use Invoize\Api\Api;
use Invoize\Models\Receipt;
use Invoize\Models\Invoice;
use WP_REST_Request;

class ReceiptAPI extends Api
{
    protected string $routeName = 'receipt';

    public function list(WP_REST_Request $request)
    {

        $search = sanitize_text_field($request->get_param('search'));
        $tab = sanitize_text_field($request->get_param('tab'));
        // $isRecurring = filter_var(
        //     $request->get_param('recurring'),
        //     FILTER_VALIDATE_BOOLEAN
        // );

        // receipt data is equal to invoice data
        $invoices = Invoice::query()->has('receipt')->with('metas');

        try {
            // if ($isRecurring) {
            //     // Recurring
            //     $invoices->whereHas('metas', function ($meta) {
            //         $meta
            //             ->where('meta_key', 'recurring')
            //             ->whereNotNull('meta_value');
            //     });
            // } else {
            //     // One-time
            //     $invoices->whereHas('metas', function ($meta) {
            //         $meta
            //             ->where('meta_key', 'recurring')
            //             ->whereNull('meta_value');
            //     });
            // }

            if (!empty($tab) && $tab != 'all') {
                $invoices->whereHas('receipt', function ($meta) use ($tab) {
                    $meta->where('post_excerpt', $tab);
                });
            }

            // search based on: post_title, post_content, invoice_number
            if ($search) {
                $invoices->where(function ($query) use ($search) {
                    $query
                        ->where('post_content', 'LIKE', '%' . $search . '%')
                        ->orWhere('post_title', 'LIKE', '%' . $search . '%');
                })->orWhereHas('metas', function ($query) use ($search) {
                    $query->where('meta_key', 'invoice_number')
                        ->where('meta_value', 'LIKE', '%' . $search . '%');
                });
            }

            $page = (int) $request->get_param('page') ?: 1;
            $per_page = (int) $request->get_param('per_page') ?: 10;
            $start_index = ($page - 1) * $per_page;
            $total_items = $invoices->count();
            $total_pages = ceil($total_items / $per_page);

            $invoices = $invoices
                ->orderBy('post_date', 'desc')
                ->skip($start_index)
                ->take($per_page)
                ->get()
                ->map(function ($invoice) {
                    $receipt = $invoice->receipt;
                    $receiptNumber = $receipt->metas()->where('meta_key', 'number')->first();
                    if ($receiptNumber) {
                        $receiptNumber = $receiptNumber->meta_value;
                    }
                    $receiptNumber = $receiptNumber ? "#$receiptNumber" : $receipt->post_title;

                    $metas = $invoice->metas;
                    $meta = [];
                    foreach ($metas as $m) {
                        $meta[$m->meta_key] = is_serialized($m->meta_value)
                            ? unserialize($m->meta_value)
                            : $m->meta_value;
                    }

                    $invoiceNumber = isset($meta['number'])
                        ? "#{$meta['number']}"
                        : $invoice->post_title;

                    return [
                        'token' => $meta['token'],
                        'receiptId' => $receipt->ID,
                        'receiptNumber' => $receiptNumber,
                        'invoiceId' => $invoice->ID,
                        'invoiceNumber' => $invoiceNumber,
                        'client' => $invoice->post_content,
                        'currency' => $meta['currency'],
                        'createdDate' => $meta['paid_date'],
                        'recurring' => $meta['recurring'],
                        'paymentStatus' => $meta['payment_status'],
                        'invoiceStatus' => $meta['invoice_status'],
                        'invoiceTab' => $meta['tab'],
                        'receiptTab' => $receipt->post_excerpt
                    ];
                });
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage()
            ], 500);
        }

        return $this->response([
            'items' => $invoices,
            'page' => $page,
            'per_page' => $per_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
        ]);
    }

    public function detail(WP_REST_Request $request)
    {
        $token = sanitize_text_field($request->get_param('invoizeToken'));

        if (!$token) {
            return $this->response([
                'message' => 'Missing parameter'
            ], 422);
        }

        $inv = Invoice::whereHas('metas', function ($meta) use ($token) {
            $meta->where('meta_key', 'token')
                ->where('meta_value', $token);
        })->first();

        $receipt = $inv->receipt;

        if (!$inv) {
            return $this->response([
                'message' => 'Invoice not found',
            ], 404);
        }

        if (!$receipt) {
            return $this->response([
                'message' => 'Invoice not found',
            ], 404);
        }

        try {
            // TODO: Optimize this
            $metas = [];
            foreach ($inv->metas as $m) {
                $metas[$m->meta_key] = is_serialized($m->meta_value)
                    ? unserialize($m->meta_value)
                    : $m->meta_value;
            }

            $receiptMeta = $receipt->metas()->first();
            $receiptTerms = $receiptMeta->meta_value;

            $invoiceToken = $metas['token'] ?? false;
            if ($invoiceToken !== $token) {
                return $this->response([
                    'message' => 'Invalid token'
                ], 403);
            }

            $res = [
                'id' => $receipt->ID,
                'receiptNumber' => $receipt->getReceiptNumber(),
                'invoiceNumber' => $inv->post_title,
                'token' => $metas['token'],
                'business' => $metas['invoice']['business'],
                'client' => $metas['invoice']['client'],
                'orderDate' => $metas['invoice']['orderDate'],
                'invoiceDate' => $metas['invoice']['invoiceDate'],
                'dueDate' => $metas['invoice']['dueDate'],
                'products' => $metas['invoice']['products'],
                'payments' => $metas['invoice']['payments'],
                'currency' => $metas['invoice']['currency'],
                'subtotal' => $metas['invoice']['subtotal'],
                'total' => $metas['invoice']['total'],
                'discounts' => $metas['invoice']['discount'],
                'taxes' => $metas['invoice']['tax'],
                'reminders' => $metas['invoice']['reminders'],
                'recurring' => $metas['recurring'],
                'invoiceNote' => $metas['invoice_note'],
                'receiptTerms' => $receiptTerms,
                'paymentStatus' => $metas['payment_status'],
                'invoiceStatus' => $metas['invoice_status'],
                'tab' => $metas['tab'],
                'paidDate' => $metas['paid_date'] ?? NULL,
                'user' => $metas['user']
            ];
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage()
            ], 500);
        }

        return $this->response($res);
    }

    public function registerRoutes()
    {
        $this->registerGetRequest('list', [
            'callback' => [$this, 'list']
        ]);

        $this->registerGetRequest('detail', [
            'callback' => [$this, 'detail']
        ]);
    }
}
