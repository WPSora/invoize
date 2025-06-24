<?php

namespace Invoize\Api\Quotation;

use Invoize\Api\Api;
use Invoize\Classes\Log;
use Invoize\Classes\Reminder;
use Invoize\Models\Invoice\ActionHistory;
use Invoize\Models\Invoice\Business;
use Invoize\Models\Invoice\Client;
use Invoize\Models\Invoice\Currency;
use Invoize\Models\Invoice\Discounts;
use Invoize\Models\Invoice\Products;
use Invoize\Models\Invoice\Reminders;
use Invoize\Models\Invoice\Taxes;
use Invoize\Models\Invoice\User;
use Invoize\Models\Quotation;
use WP_REST_Request;

class QuotationAPI extends Api
{
    protected string $routeName = 'quotation';

    public function sendMail(WP_REST_Request $request)
    {
        $token = sanitize_text_field($request->get_param('token'));

        if (!$token) {
            return $this->response([
                'message' => 'Token is required',
            ], 400);
        }

        $quotation = Quotation::findByToken($token);

        if (!$quotation) {
            return $this->response([
                'message' => 'Quotation not found',
            ], 404);
        }

        try {
            Quotation::sendMail($quotation->ID, $quotation->getMeta('status'));
            Log::action('Sending quotation email. ID' . $quotation->ID);
            return $this->response([
                'message' => "Email sent successfully",
            ]);
        } catch (\Exception $e) {
            Log::emailError('ID: ' . $quotation->ID . '. ' . $e->getMessage());
            return $this->response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function toInvoice(WP_REST_Request $request)
    {
        $token = sanitize_text_field($request->get_param('token'));

        if (!$token) {
            return $this->response([
                'message' => 'Token is required',
            ], 400);
        }

        $quotation = Quotation::whereHas('metas', function ($query) use ($token) {
            $query->where('meta_key', 'token')->where('meta_value', $token);
        })->first();

        if (!$quotation) {
            return $this->response([
                'message' => 'Quotation not found',
            ], 404);
        }

        try {
            $invoice = $quotation->convertToInvoice();
            return $this->response([
                'invoice' => $invoice,
                'message' => "Invoice created successfully",
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create Invoice from Quotation. ID: ' . $quotation->ID . '. ' . $e->getMessage());
            return $this->response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function detail(WP_REST_Request $request)
    {
        $token = sanitize_text_field($request->get_param('token'));

        if (!$token) {
            return $this->response([
                'message' => 'Token is required',
            ], 400);
        }

        $quotation = Quotation::whereHas('metas', function ($query) use ($token) {
            $query->where('meta_key', 'token')->where('meta_value', $token);
        })->first();

        if (!$quotation) {
            return $this->response([
                'message' => 'Quotation not found',
            ], 404);
        }

        return $this->response($quotation->detail());
    }

    public function list(WP_REST_Request $request)
    {
        $status = sanitize_text_field($request->get_param('tab'));

        $quotations = Quotation::whereHas('metas', function ($query) use ($status) {
            $query->where('meta_key', 'status')->where('meta_value', $status);
        });

        $page           = (int) $request->get_param('page') ?: 1;
        $per_page       = (int) $request->get_param('per_page') ?: 10;
        $start_index    = ($page - 1) * $per_page;
        $total_items    = $quotations->count();
        $total_pages    = ceil($total_items / $per_page);

        $quotations = $quotations->orderBy('post_date', 'desc')
            ->skip($start_index)
            ->take($per_page)
            ->get()
            ->map(function ($record) {
                return [
                    'id'                => $record->ID,
                    'status'            => $record->getMeta('status'),
                    'currency'          => invoize_mb_unserialize($record->getMeta('currency')),
                    'quotationNumber'   => $record->getMeta('prefix') . $record->getMeta('number'),
                    'client'            => invoize_mb_unserialize($record->getMeta('client')),
                    'token'             => $record->getMeta('token'),
                    'payment'           => invoize_mb_unserialize($record->getMeta('payments')),
                    'quotationDate'     => $record->getMeta('quotation_date'),
                    'dueDate'           => $record->getMeta('due_date'),
                    'total'             => $record->getMeta('total'),
                ];
            });

        return $this->response([
            'items' => $quotations->toArray(),
            'page' => $page,
            'per_page' => $per_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
        ]);
    }


    public function create(WP_REST_Request $request)
    {

        $client     = Client::instance()->setContent($request->get_param('client'));
        $business   = Business::instance()->setContent($request->get_param('business'));
        $products   = Products::instance()->setContent($request->get_param('products'));
        $currency   = Currency::instance()->setContent($request->get_param('currency'));
        $discounts  = Discounts::instance()->setContent($request->get_param('discount'));
        $taxes      = Taxes::instance()->setContent($request->get_param('tax'));
        $reminders  = Reminders::instance()->setContent($request->get_param('reminder'));
        $user       = User::instance()->setContent(wp_get_current_user())->getContent();
        $sendEmail  = sanitize_text_field($request->get_param('sendEmail'));

        $actionHistory  = ActionHistory::instance()
            ->setUser($user)
            ->setTo('active')
            ->setMessage($user['name'] . ' has created this quotation')
            ->getContent();


        try {
            $quotation = Quotation::create([
                'post_title' => sanitize_text_field($request->get_param('id')),
                'post_content' => $client->name,
            ]);

            $quotation->setMeta([
                'status'            => sanitize_text_field($request->get_param('status')),
                'prefix'            => sanitize_text_field($request->get_param('prefix')),
                'token'             => invoizeGenerateToken($request->get_param('id'), $client->id),
                'number'            => sanitize_text_field($request->get_param('number')),
                'products'          => $products->getContent(),
                'currency'          => $currency->getContent(),
                'discount'          => $discounts->getContent(),
                'tax'               => $taxes->getContent(),
                'business'          => $business->getContent(),
                'client'            => $client->getContent(),
                'payments'          => $request->get_param('payments'), // sanitize ?
                'quotation_date'    => sanitize_text_field($request->get_param('quotationDate')),
                'due_date'          => sanitize_text_field($request->get_param('dueDate')),
                'quotation_note'    => [
                    'note' => sanitize_text_field($request->get_param('note')),
                    'terms' => sanitize_text_field($request->get_param('terms')),
                    'internalNote' => sanitize_text_field($request->get_param('internalNote')),
                ],
                'total'             => (float) sanitize_text_field($request->get_param('total')),
                'subtotal'          => (float) sanitize_text_field($request->get_param('subtotal')),
                'reminder_before'   => $reminders->getContent()['before'],
                'reminder_after'    => $reminders->getContent()['after'],
                'reminder_for_admin'  => $reminders->getContent()['forAdmin'],
                'reminder_for_client' => $reminders->getContent()['forClient'],
                'user'              => $user,
                'action_history'    => [$actionHistory]
            ]);

            invoizeUpdateOption('quotation.startFromNumber', (int) sanitize_text_field($request->get_param('number') + 1));

            Reminder::schedule_reminder();

            Log::action('Quotation is created. ID: ' . $quotation->ID);
            return $this->response([
                'token' => $quotation->getMeta('token'),
                'message' => "Quotation created successfully",
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create Quotation. ' . $e->getMessage());
            return $this->response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteAll()
    {
        try {
            $all  = Quotation::all();
            foreach ($all as $inv) {
                $inv->metas()->delete();
            }

            Quotation::query()->delete();
            return $this->response(['message' => 'success']);
        } catch (\Exception $e) {
            return $this->response(['message' => $e->getMessage()], 500);
        }
    }


    public function archive(WP_REST_Request $request)
    {
        $token  = sanitize_text_field($request->get_param('token'));
        if (!$token) {
            return $this->response(['message' => 'Token is required'], 400);
        }

        $quotation = Quotation::whereHas('metas', function ($query) use ($token) {
            $query->where('meta_key', 'token')->where('meta_value', $token);
        })->first();

        if (!$quotation) {
            return $this->response(['message' => 'Quotation not found'], 404);
        }

        try {
            $quotation->updateMeta([
                'status' => Quotation::STATUS_ARCHIVE,
            ]);

            $quotation->saveActionHistory('active', 'archive', 'archived this quotation');

            Log::action('Quotation is changed to archived. ID: ' . $quotation->ID);

            return $this->response(['message' => 'Quotation archived successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to archive Quotation. ID: ' . $quotation->ID . '. ' . $e->getMessage());
            return $this->response(['message' => $e->getMessage()], 500);
        }
    }

    public function registerRoutes()
    {
        $this->registerGetRequest('detail', [
            'callback' => [$this, 'detail'],
        ]);

        $this->registerGetRequest('list', [
            'callback' => [$this, 'list'],
        ]);

        // $this->registerGetRequest('delete-all', [
        //     'callback' => [$this, 'deleteAll'],
        // ]);

        $this->registerPostRequest('archive', [
            'callback' => [$this, 'archive'],
        ]);

        $this->registerPostRequest('to-invoice', [
            'callback' => [$this, 'toInvoice'],
        ]);

        $this->registerPostRequest('create', [
            'callback' => [$this, 'create'],
        ]);

        $this->registerPostRequest('send-mail', [
            'callback' => [$this, 'sendMail'],
        ]);
    }
}
