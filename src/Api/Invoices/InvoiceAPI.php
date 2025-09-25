<?php

namespace Invoize\Api\Invoices;

use Exception;
use Illuminate\Support\Carbon;
use Invoize\Api\Api;
use Invoize\Classes\Log;
use Invoize\Classes\Summary\Summary;
use Invoize\Models\Invoice;
use Invoize\Models\Invoice\ActionHistory;
use Invoize\Models\Invoice\Business;
use Invoize\Models\Invoice\Client;
use Invoize\Models\Invoice\Currency;
use Invoize\Models\Invoice\Discounts;
use Invoize\Models\Invoice\Payment\PaymentParameter;
use Invoize\Models\Invoice\Payment\Payments;
use Invoize\Models\Invoice\Products;
use Invoize\Models\Invoice\Reminders;
use Invoize\Models\Invoice\Taxes;
use Invoize\Models\Invoice\User;
use Invoize\Models\Recurring\Recurring;
use Invoize\Models\Setting;
use Invoize\Models\States\Invoice\PaymentState\InvoiceUnpaidState;
use Invoize\Payments\Constant\PaymentError;
use WP_REST_Request;

class InvoiceAPI extends Api
{

    protected string $routeName = 'invoice';


    public function list(WP_REST_Request $request)
    {
        $paymentStatus  = sanitize_text_field($request->get_param('paymentStatus'));
        $invoiceStatus  = sanitize_text_field($request->get_param('invoiceStatus'));
        $tab            = sanitize_text_field($request->get_param('tab'));
        $search         = sanitize_text_field($request->get_param('search'));
        $recurring      = filter_var($request->get_param('recurring'), FILTER_VALIDATE_BOOLEAN);
        $clientId       = sanitize_text_field($request->get_param('clientId'));
        $invoices       = Invoice::query()->with('metas');

        try {
            if ($clientId) {
                $invoices->whereHas('metas', function ($meta) use ($clientId) {
                    $meta->where('meta_key', 'client_id')
                        ->where('meta_value', $clientId);
                });
            }

            if ($recurring) {
                $invoices->whereHas('metas', function ($meta) {
                    $meta->where('meta_key', 'recurring_id')
                        ->whereNotNull('meta_value');
                });
            }

            if ($tab && $tab == 'all') {
                $invoices->whereHas('metas', function ($meta) {
                    $meta
                        ->where('meta_key', 'tab')
                        ->where(function ($query) {
                            $query->where('meta_value', Invoice::PAID)
                                ->orWhere('meta_value', Invoice::UNPAID);
                        });
                });
            } else if ($tab) {
                $invoices->whereHas('metas', function ($meta) use ($tab) {
                    $meta
                        ->where('meta_key', 'tab')
                        ->where('meta_value', $tab);
                });
            }


            if ($paymentStatus) {
                $invoices->whereHas('metas', function ($meta)
                use ($paymentStatus) {
                    $meta
                        ->where('meta_key', 'payment_status')
                        ->where('meta_value', $paymentStatus);
                });
            }

            if ($invoiceStatus) {
                if ($invoiceStatus == 'expired') {
                    $invoices->whereHas('metas', function ($meta) {
                        $meta
                            ->where('meta_key', 'due_date')
                            ->where(
                                'meta_value',
                                '<',
                                Carbon::now()->format('Y-m-d')
                            );
                    })->whereHas('metas', function ($meta) {
                        $meta
                            ->where('meta_key', 'payment_status')
                            ->where('meta_value', 'unpaid');
                    });
                } else {
                    $invoices->whereHas('metas', function ($meta)
                    use ($invoiceStatus) {
                        $meta
                            ->where('meta_key', 'invoice_status')
                            ->where('meta_value', $invoiceStatus);
                    });
                }
            }

            if ($search) {
                $invoices->where(function ($query) use ($search) {
                    $query
                        ->where('post_content', 'LIKE', '%' . $search . '%')
                        ->orWhere('post_title', 'LIKE', '%' . $search . '%');
                });
            }

            $page           = (int) $request->get_param('page') ?: 1;
            $per_page       = (int) $request->get_param('per_page') ?: 10;
            $start_index    = ($page - 1) * $per_page;
            $total_items    = $invoices->count();
            $total_pages    = ceil($total_items / $per_page);

            $invoices = $invoices
                ->orderBy('post_date', 'desc')
                ->skip($start_index)
                ->take($per_page)
                ->get()
                ->map(function ($invoice) {
                    $metas = $invoice->metas;
                    $meta = [];
                    foreach ($metas as $m) {
                        $meta[$m->meta_key] = is_serialized($m->meta_value)
                            ? invoize_mb_unserialize($m->meta_value)
                            : $m->meta_value;
                    }

                    $number = isset($meta['number']) ? "#{$meta['number']}" : $invoice->post_title;
                    $client = $meta['client']['name'];
                    $total = $meta['total'];
                    $currency = $meta['currency'];
                    $payment = $meta['payments'];
                    $dueDate = $meta['due_date'];
                    $paidDate = isset($meta['paid_date']) ? $meta['paid_date'] : null;
                    $invoiceDate = $meta['invoice_date'];
                    $recurring = [
                        'name' => $meta['recurring_name'],
                        'start' => $meta['recurring_start'],
                        'end' => $meta['recurring_end_in'],
                        'interval' => $meta['recurring_interval']
                    ];
                    $paymentStatus = $meta['payment_status'];
                    $invoiceStatus = $meta['invoice_status'];
                    $tab = $meta['tab'];
                    $isSent = isset($meta['is_email_sent']) && !empty($meta['is_email_sent'])
                        ? (bool) $meta['is_email_sent'] : false;
                    $isWoocommerce = isset($meta['wc_order_id']) && !empty($meta['wc_order_id'])
                        ? true : false;

                    return [
                        'id' => $invoice->ID,
                        'token' => $meta['token'],
                        'invoiceId' => $number,
                        'client' => $client,
                        'total' => $total,
                        'currency' => $currency,
                        'payment' => $payment,
                        'paidDate' => $paidDate,
                        'dueDate' => $dueDate,
                        'invoiceDate' => $invoiceDate,
                        'recurring' => $recurring,
                        'paymentStatus' => $paymentStatus,
                        'invoiceStatus' => $invoiceStatus,
                        'tab' => $tab,
                        'isSent' => $isSent,
                        'isWoocommerce' => $isWoocommerce,
                    ];
                });
        } catch (\Exception $e) {
            Log::error($e->getMessage());
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


    /**
     * TODO: Add Generate Payment Link
     */
    public function create(WP_REST_Request $request)
    {
        $isEmailSent = false;
        $isCreateReceipt = false;
        $invoice = $request->get_params();

        $requiredFields = [
            'business',
            'client',
            'status',
            'orderDate',
            'invoiceDate',
            'dueDate',
            'products',
            'billedToSameAsClient',
            'payments',
            'currency',
            'subtotal',
            'total'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($invoice[$field])) {
                return $this->response([
                    'message' => "Missing required field: $field"
                ], 422);
            }
        }

        $id             = sanitize_text_field($invoice['id']); // string, not integer, eg: Invo-123
        $prefix         = sanitize_text_field($invoice['prefix']);
        $number         = sanitize_text_field($invoice['number']);
        $status         = sanitize_text_field($invoice['status']);
        $orderDate      = sanitize_text_field($invoice['orderDate']);
        $invoiceDate    = sanitize_text_field($invoice['invoiceDate']);
        $dueDate        = sanitize_text_field($invoice['dueDate']);
        $note           = sanitize_textarea_field($invoice['note'] ?? '');
        $terms          = sanitize_textarea_field($invoice['terms'] ?? '');
        $internalNote   = sanitize_textarea_field($invoice['internalNote'] ?? '');
        $subtotal       = (float) $invoice['subtotal'] ?: 0;
        $total          = (float) $invoice['total'] ?: 0;
        $business       = Business::instance()->setContent($invoice['business'])->getContent();
        $client         = Client::instance()->setContent($invoice['client'])->getContent();
        $products       = Products::instance()->setContent($invoice['products'])->getContent();
        $currency       = Currency::instance()->setContent($invoice['currency'])->getContent();
        $discounts      = Discounts::instance()->setContent($invoice['discount'])->getContent();
        $taxes          = Taxes::instance()->setContent($invoice['tax'])->getContent();
        $token          = invoizeGenerateToken($id, $client['id']);

        $billedTo               = ["name" => null, "detail" => null];
        $billedToSameAsClient   = isset($invoice['billedToSameAsClient'])
            ? sanitize_text_field($invoice['billedToSameAsClient']) != "false"
            : true;

        if (!$billedToSameAsClient && isset($invoice['billedTo'])) {
            $billedTo = [
                'name' => isset($invoice['billedTo']['name'])
                    ? sanitize_text_field($invoice['billedTo']['name'])
                    : null,
                'detail' => isset($invoice['billedTo']['detail'])
                    ? sanitize_textarea_field($invoice['billedTo']['detail'])
                    : null,
            ];
            if (!$billedTo['name']) {
                return $this->response([
                    'message' => "Billed to 'name' can't be empty"
                ], 422);
            }
        }

        $reminders      = Reminders::instance()
            ->setContent($invoice['reminder'] ?? null, $dueDate)
            ->getContent();

        $user = User::instance()->setContent(wp_get_current_user())->getContent();
        $actionHistory  = ActionHistory::instance()
            ->setUser($user)
            ->setTo($status)
            ->setMessage($user['name'] . ' has created this invoice')
            ->getContent();

        $payments = $invoice['payments'];

        // if (!invoizeGetOption('payment.enablePaymentPage', false)) {
        //     $paymentParameter = PaymentParameter::instance()
        //         ->setId($id)
        //         ->setToken($token)
        //         ->setCurrency($currency['name'])
        //         ->setTotal($total)
        //         ->setDueDate($dueDate)
        //         ->setCustomer($client);

        //     try {
        //         $payments = Payments::instance()
        //             ->setParameter($paymentParameter)
        //             ->setContent($invoice['payments'])
        //             ->checkError()
        //             ->getContent();
        //     } catch (\Exception $e) {
        //         Log::error($e->getMessage());
        //         return $this->response(['message' => $e->getMessage()], $e->getCode());
        //     }
        // }

        try {
            $invoiceId = Invoice::generateInvoice([
                'id' => $id,
                'prefix' => $prefix,
                'number' => $number,
                'token' => $token,
                'business' => $business,
                'client' => $client,
                'billedTo' => $billedTo,
                'billedToSameAsClient' => $billedToSameAsClient,
                'paymentStatus' => $status,
                'invoiceStatus' => Invoice::ACTIVE,
                'tab' => $status,
                'orderDate' => $orderDate,
                'invoiceDate' => $invoiceDate,
                'dueDate' => $dueDate,
                'products' => $products,
                'payments' => $payments,
                'currency' => $currency,
                'subtotal' => $subtotal,
                'total' => $total,
                'discounts' => $discounts,
                'taxes' => $taxes,
                'reminders' => $reminders,
                'note' => $note,
                'terms' => $terms,
                'internalNote' => $internalNote,
                'user' => $user,
                'actionHistory' => $actionHistory
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->response(['message' => $e->getMessage()], 500);
        }

        if ($status == Invoice::PAID) {
            try {
                Invoice::createReceipt($invoiceId);
                $isCreateReceipt = true;
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                error_log("Failed to create receipt. InvoiceID: $invoiceId. Message: {$e->getMessage()}");
            }
        }

        try {
            if (filter_var($request->get_param('email'), FILTER_VALIDATE_BOOLEAN)) {
                Invoice::sendMail($invoiceId, $status);
                $isEmailSent = true;
            }
            return $this->response([
                "message" => "success",
                "emailSent" => $isEmailSent,
                "receiptCreated" => $isCreateReceipt,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return $this->response([
                'message' => "Invoice created. But failed to send email. Error message: " . $e->getMessage(),
                'emailSent' => false,
                'receiptCreated' => $isCreateReceipt,
                'token' => $token,
            ], 200);
        }
    }


    public function createFromWc(WP_REST_Request $request)
    {
        $orderId = sanitize_text_field($request->get_param(('orderId')));
        if (!$orderId) {
            return $this->response(['message' => 'Missing parameter'], 422);
        }

        try {
            $response = Invoice::createFromWc($orderId, true);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error($message);
            if ($message == PaymentError::PAYPAL_UNSUPPORTED_CURRENCY) {
                return $this->response([
                    'message' => PaymentError::PAYPAL_UNSUPPORTED_CURRENCY
                ], 400);
            }
            if ($message == PaymentError::XENDIT_UNSUPPORTED_CURRENCY) {
                return $this->response([
                    'message' => PaymentError::XENDIT_UNSUPPORTED_CURRENCY
                ], 400);
            }
            if ($message == PaymentError::INVALID_PAYPAL_KEY) {
                return $this->response([
                    'message' => PaymentError::INVALID_PAYPAL_KEY
                ], 400);
            }
            return $this->response(['message' => $message], $e->getCode());
        }

        return $this->response([
            'message' => 'success',
            'id' => $response['id'],
            'token' => $response['token'],
        ]);
    }


    public function detail(WP_REST_Request $request)
    {
        $token = sanitize_text_field($request->get_param('invoizeToken'));

        if (!$token) {
            return $this->response(['message' => 'Missing parameter'], 422);
        }

        $inv = Invoice::findByToken($token);

        if (!$inv) {
            return $this->response([
                'message' => 'Invoice not found',
            ], 404);
        }

        return $this->response($inv->detail());
    }


    public function update(WP_REST_Request $req)
    {
        // Update status to paid, unpaid, cancel, archive, trashed
        // can use either invoice ID or token to update it
        $invoiceId      = sanitize_text_field($req->get_param('id'));
        $token          = sanitize_text_field($req->get_param('token'));
        $paymentParam   = sanitize_text_field($req->get_param('paymentStatus'));
        $invoiceParam   = sanitize_text_field($req->get_param('invoiceStatus'));

        if (!$invoiceId && !$token) {
            return $this->response(['message' => 'Missing parameter'], 422);
        }

        if (!$paymentParam && !$invoiceParam) {
            return $this->response(['message' => "Missing parameter!"], 422);
        }

        $invoice = null;

        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
        } else if ($token) {
            $invoice = Invoice::whereHas('metas', function ($metas) use ($token) {
                $metas->where('meta_key', 'token')
                    ->where('meta_value', $token);
            })->first();
        }

        if (!$invoice) {
            return $this->response(['message' => 'Invoice not found'], 404);
        }

        $hasUpdated     = false;
        $paymentState   = $invoice->getPaymentState();
        $invoiceState   = $invoice->getInvoiceState();

        try {
            if ($paymentParam == Invoice::UNPAID) {
                $paymentState->unpay();
                $hasUpdated = true;
            }

            if ($paymentParam == Invoice::PAID) {
                // if it is not InvoiceUnpaidState, it means its already in Paid State, which mean
                // we should not update it to Paid again. This is for case if other user or
                // Customer already Paid the invoice but the user try to update the invoice to
                // Paid again because the front end is still not updating.
                if ($paymentState instanceof InvoiceUnpaidState) {
                    $paymentState->pay();
                    $isSendEmail = filter_var($req->get_param('email'), FILTER_VALIDATE_BOOLEAN);
                    if ($isSendEmail) {
                        try {
                            Invoice::sendMail($invoice->ID, Invoice::PAID);
                        } catch (\Exception $e) {
                            error_log("Failed to send email. Invoice ID : $invoiceId. Message: " . $e->getMessage());
                        }
                    }
                }
                $hasUpdated = true;
            }

            if ($invoiceParam == Invoice::ARCHIVED) {
                $invoiceState->archive();
                $hasUpdated = true;
            }

            if ($invoiceParam == Invoice::ACTIVE) {
                $invoiceState->activate();
                $hasUpdated = true;
            }

            if ($invoiceParam == Invoice::CANCELLED) {
                $invoiceState->cancel();
                $hasUpdated = true;
            }

            if ($invoiceParam == Invoice::TRASHED) {
                $invoiceState->trash();
                $hasUpdated = true;
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->response(['message' => $e->getMessage()], 400);
        }

        if (!$hasUpdated) {
            Log::error('Update not applied. Please check your parameter');
            return $this->response([
                'message' => 'Update not applied. Please check your parameter'
            ], 400);
        }

        return $this->response(['message' => 'Update invoice status success']);
    }

    public function edit(WP_REST_Request $request)
    {
        $id = sanitize_text_field($request->get_param('id'));
        if (!$id) {
            return $this->response(['message' => 'Missing parameter'], 422);
        }

        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->response(['message' => 'Invoice not found'], 404);
        }

        $status = $invoice->metas()->where('meta_key', 'payment_status')->first()->meta_value;
        if ($status == Invoice::PAID) {
            return $this->response(['message' => 'Forbidden to edit this invoice'], 403);
        }

        $params = $request->get_params();
        $requiredFields = [
            'business',
            'client',
            'orderDate',
            'invoiceDate',
            'dueDate',
            'products',
            'billedToSameAsClient',
            'payments',
            'currency',
            'subtotal',
            'total'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($params[$field])) {
                return $this->response(['message' => "Missing required field: $field"], 422);
            }
        }

        try {
            $billedTo               = ["name" => null, "detail" => null];
            $billedToSameAsClient   = isset($params['billedToSameAsClient'])
                ? sanitize_text_field($params['billedToSameAsClient']) != "false"
                : false;

            $params['billedToSameAsClient'] = $billedToSameAsClient;

            if (!$billedToSameAsClient && isset($params['billedTo'])) {
                $billedTo = [
                    'name' => isset($params['billedTo']['name'])
                        ? sanitize_text_field($params['billedTo']['name'])
                        : null,
                    'detail' => isset($params['billedTo']['detail'])
                        ? sanitize_textarea_field($params['billedTo']['detail'])
                        : null,
                ];
                $params['billedTo'] = $billedTo;
                if (!$billedTo['name']) {
                    return $this->response([
                        'message' => "Billed to 'name' can't be empty"
                    ], 422);
                }
            }
            $token = $invoice->editInvoice($params);
        } catch (\Exception $e) {
            return $this->response(['message' => $e->getMessage()], 500);
        }

        return $this->response([
            'message' => 'Success',
            'token' => $token,
        ]);
    }


    public function delete(WP_REST_Request $request)
    {
        try {
            $id = sanitize_text_field($request->get_param('id'));
            if (!$id) {
                return $this->response(['message' => "Missing parameter"], 422);
            }

            if (!Invoice::where('ID', $id)->exists()) {
                return $this->response(['message' => "Invoice not found"], 404);
            }

            wp_delete_post($id);
            Log::action('Invoice is deleted. ID: ' . $id);
        } catch (\Exception $e) {
            Log::error('Failed to delete invoice. ' . $e->getMessage());
            return $this->response(['message' => $e->getMessage()], 500);
        }

        return $this->response(
            ['message' => 'Delete Invoice Success'],
        );
    }


    public function duplicate(WP_REST_Request $request)
    {
        $id = sanitize_text_field($request->get_param('id'));
        if (!$id) {
            return $this->response(['message' => 'Missing id.'], 422);
        }

        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->response(['message' => 'Invoice not found'], 404);
        }

        try {
            $newId = $invoice->duplicate();
        } catch (Exception $e) {
            Log::error('Failed to duplicate invoice. ID: ' . $invoice->ID . '. ' . $e->getMessage());
            return $this->response(['message' => 'Failed to duplicate invoice']);
        }

        return $this->response(['message' => 'success', 'id' => $newId]);
    }


    public function regenerate(WP_REST_Request $request)
    {
        $id = sanitize_text_field($request->get_param('id'));
        if (!$id) {
            return $this->response(['message' => 'Missing id'], 422);
        }

        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->response(['message' => 'Invoice not found'], 404);
        }

        $status = $invoice->metas()->where('meta_key', 'payment_status')->first();
        if ($status->meta_value != Invoice::UNPAID) {
            return $this->response(['message' => 'Forbidden to regenerate invoice'], 403);
        }

        try {
            $newId = $invoice->regenerate();
        } catch (\Exception $e) {
            Log::error('Failed to regenerate invoice. ID: ' . $invoice->ID . '. ' . $e->getMessage());
            return $this->response(['message' => $e->getMessage()], $e->getCode());
        }

        return $this->response(['message' => 'success', 'id' => $newId]);
    }


    public function listExpiredSoon(WP_REST_Request $request)
    {
        $perPage = (int) $request->get_param('per_page') ?: 10;
        return $this->response(['data' => Invoice::getExpiredSoon($perPage)]);
    }


    public function monthlySummary()
    {
        return $this->response([
            'data' => Invoice::getSummary(),
            'default_currency' => Setting::getDefaultCurrency()
        ]);
    }


    public function recalculateSummary(WP_REST_Request $request)
    {
        $yearParam = sanitize_text_field($request->get_param('year'));
        try {
            $now    = Carbon::now();
            $year   = $yearParam ?: $now->year;
            Setting::recalculateSummary($now->month, $year, Summary::YEAR_SYNC);
        } catch (\Exception $e) {
            Log::error('Failed to calculate summary.' . $e->getMessage());
            return $this->response(['message' => $e->getMessage()], $e->getCode());
        }

        return $this->response(['message' => 'Success']);
    }


    public function sendMail(WP_REST_Request $request)
    {
        $id         = sanitize_text_field($request->get_param('id'));
        $status     = sanitize_text_field($request->get_param('status'));
        if (!$id || !$status) {
            return $this->response(['message' => 'Missing parameter'], 422);
        }

        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->response(['message' => 'Invoice not found'], 404);
        }

        try {
            Invoice::sendMail($id, $status);
        } catch (\Exception $e) {
            return $this->response(['message' => $e->getMessage()], 500);
        }

        return $this->response(['message' => 'success']);
    }


    public function toRecurring(WP_REST_Request $request)
    {
        $recurring = $request->get_params();
        $requiredFields = [
            'token',
            'dueDateInterval',
            'recurring',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($recurring[$field])) {
                return $this->response([
                    'message' => "Missing required field: $field"
                ], 422);
            }
        }

        $recurringModel = Recurring::instance()
            ->setContent($recurring['recurring'])
            ->getContent();

        try {
            $inv = Invoice::findByToken(sanitize_text_field($recurring['token']));

            if (!$inv) {
                return $this->response(['message' => 'Invoice not found'], 404);
            }

            if ($inv->isRecurring()) {
                return $this->response(['message' => 'Invoice is already a recurring invoice'], 403);
            }

            $inv->toRecurring([
                'id' => $recurringModel['name'],
                'recurring' => $recurringModel,
                'dueDateInterval' => $recurring['dueDateInterval']
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update invoice to recurring. ID: ' . $inv->ID . '. ' . $e->getMessage());
            return $this->response(['message' => $e->getMessage()], $e->getCode());
        }

        return $this->response(['message' => 'success']);
    }



    public function deleteAll()
    {
        try {
            $all  = Invoice::all();
            foreach ($all as $inv) {
                $inv->metas()->delete();
            }

            Invoice::query()->delete();
            return $this->response(['message' => 'success']);
        } catch (\Exception $e) {
            return $this->response(['message' => $e->getMessage()], 500);
        }
    }


    public function deleteAllSummary()
    {
        try {
            Setting::tab('summary')->delete();
            return $this->response(['message' => 'success']);
        } catch (\Exception $e) {
            return $this->response(['message' => $e->getMessage()], 500);
        }
    }


    public function registerRoutes()
    {
        // $this->registerGetRequest('delete-all', [
        //     'callback' => [$this, 'deleteAll'],
        // ]);

        $this->registerGetRequest('summary/delete-all', [
            'callback' => [$this, 'deleteAllSummary'],
        ]);

        $this->registerGetRequest('list', [
            'callback' => [$this, 'list'],
        ]);

        $this->registerPostRequest('create', [
            'callback' => [$this, 'create'],
        ]);

        $this->registerPostRequest('create-from-wc', [
            'callback' => [$this, 'createFromWc'],
        ]);

        $this->registerGetRequest('detail', [
            'callback' => [$this, 'detail'],
            'permission_callback' => function (WP_REST_Request $request) {
                if ($request->get_param('invoizeToken')) {
                    return true;
                }
                return false;
            }
        ]);

        $this->registerPostRequest('update', [
            'callback' => [$this, 'update'],
            'permission_callback' => function () {
                return invoizeCheckPermissionIsAllowed() || current_user_can('invoize_customer') || current_user_can('customer');
            }
        ]);

        $this->registerPostRequest('edit', [
            'callback' => [$this, 'edit'],
        ]);

        $this->registerPostRequest('delete', [
            'callback' => [$this, 'delete'],
        ]);

        $this->registerPostRequest('duplicate', [
            'callback' => [$this, 'duplicate'],
        ]);

        $this->registerPostRequest('regenerate', [
            'callback' => [$this, 'regenerate'],
        ]);

        $this->registerGetRequest('list-expired-soon', [
            'callback' => [$this, 'listExpiredSoon'],
        ]);

        $this->registerGetRequest('monthly-summary', [
            'callback' => [$this, 'monthlySummary'],
        ]);

        $this->registerGetRequest('recalculate-summary', [
            'callback' => [$this, 'recalculateSummary'],
        ]);

        $this->registerPostRequest('send-mail', [
            'callback' => [$this, 'sendMail'],
        ]);

        $this->registerPostRequest('to-recurring', [
            'callback' => [$this, 'toRecurring'],
        ]);
    }
}
