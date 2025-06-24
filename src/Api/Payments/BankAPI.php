<?php

namespace Invoize\Api\Payments;

use Invoize\Api\Api;
use Invoize\Classes\Log;
use Invoize\InvoizePlugin;
use Invoize\Models\Bank;
use Invoize\Models\Invoice\Payment\Bank\BankPayment;
use Invoize\Models\Payment;
use WP_REST_Request;

class BankAPI extends Api
{

    protected string $routeName = 'bank';



    public function list()
    {
        $banks = get_option(InvoizePlugin::getInstance()->getSlug() . '.payment.banks');
        return $this->response([
            'data' => $banks ?: [],
        ]);
    }


    public function add(WP_REST_Request $request)
    {
        $currency = $request->get_param('currency');
        $name = sanitize_text_field($request->get_param('name'));
        $type = sanitize_text_field($request->get_param('type'));
        $currencyName = sanitize_text_field($currency['name']);
        $currencySymbol = sanitize_text_field($currency['symbol']);
        $detail = sanitize_textarea_field($request->get_param('detail'));

        if (!$name || !$detail) {
            return $this->response([
                'message' => "Missing required parameter"
            ], 422);
        }

        $plugin = InvoizePlugin::getInstance()->getSlug();
        $id = get_option("$plugin.payment.nextBankId");
        $bank = [
            'id' => (int) $id ?: 1,
            'name' => $name,
            'type' => $type,
            'method' => Payment::BANK,
            'currency' => [
                'name' => $currencyName,
                'symbol' => $currencySymbol,
            ],
            'detail' => $detail,
        ];

        $banks = [];
        $banksData = get_option("$plugin.payment.banks");
        if ($banksData) {
            $banks = array_merge($banks, $banksData);
        }
        $banks[] = $bank;

        try {
            update_option("$plugin.payment.banks", $banks);
            !$id ? add_option("$plugin.payment.nextBankId", 2)
                : update_option("$plugin.payment.nextBankId", $id + 1);
        } catch (\Exception $e) {
            Log::error('Failed to save bank payment. ' . $e->getMessage());
            return $this->response([
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        return $this->response([
            'message' => "success",
            'data' => $bank,
        ]);
    }


    public function edit(WP_REST_Request $request)
    {
        $currency = $request->get_param('currency');
        $id = sanitize_text_field($request->get_param('id'));
        $name = sanitize_text_field($request->get_param('name'));
        $method = sanitize_text_field($request->get_param('method'));
        $type = sanitize_text_field($request->get_param('type'));
        $detail = sanitize_textarea_field($request->get_param('detail'));
        $currencyName = sanitize_text_field($currency['name']);
        $currencySymbol = sanitize_text_field($currency['symbol']);

        if (!$id || !$name || !$detail) {
            return $this->response([
                'message' => "Missing parameter"
            ], 400);
        }

        $plugin = InvoizePlugin::getInstance()->getSlug();
        $banks = get_option("$plugin.payment.banks");
        if (empty($banks)) {
            return $this->response(['message' => 'Bank option is not found'], 404);
        }

        $bank = array_values(array_filter($banks, fn($bank) => $bank['id'] == $id));
        if (empty($bank)) {
            return $this->response(['message' => 'Bank not found'], 404);
        }

        $newBank = [
            'id' => (int) $id,
            'name' => $name,
            'method' => Payment::BANK,
            'type' => $type,
            'currency' => [
                'name' => $currencyName,
                'symbol' => $currencySymbol,
            ],
            'detail' => $detail,
        ];

        $updatedBanks = array_map(function ($bank) use ($id, $newBank) {
            if ($bank['id'] == $id) {
                return $newBank;
            }
            return $bank;
        }, $banks);

        try {
            update_option("$plugin.payment.banks", $updatedBanks);
        } catch (\Exception $e) {
            Log::error('Failed to edit bank payment.' . $e->getMessage());
            return $this->response(['message' => $e->getMessage()], $e->getCode());
        }

        return $this->response([
            'message' => "success",
            'data' => $newBank,
        ]);
    }


    public function delete(WP_REST_Request $request)
    {
        $id = sanitize_text_field($request->get_param('id'));
        if (!$id) {
            return $this->response([
                'message' => "Missing id"
            ], 422);
        }

        $plugin = InvoizePlugin::getInstance()->getSlug();
        $banks = get_option("$plugin.payment.banks");
        if (empty($banks)) {
            return $this->response(['message' => 'Bank option is not found'], 404);
        }

        $bank = array_values(array_filter($banks, function ($bank) use ($id) {
            if ($bank['id'] == $id) {
                return $bank;
            }
        }));

        if (empty($bank)) {
            return $this->response([
                'message' => "Bank not found"
            ], 404);
        }

        $updatedBanks = array_values(array_filter($banks, function ($bank) use ($id) {
            if ($bank['id'] != $id) {
                return $bank;
            }
        }));

        try {
            update_option("$plugin.payment.banks", $updatedBanks);
        } catch (\Exception $e) {
            Log::error('Failed to delete bank payment. ' . $e->getMessage());
            return $this->response(['message' => $e->getMessage()], $e->getCode());
        }

        return $this->response(['message' => "success"]);
    }


    public function registerRoutes()
    {
        $this->registerGetRequest('list', [
            'callback' => [$this, 'list'],
        ]);

        $this->registerPostRequest('add', [
            'callback' => [$this, 'add'],
        ]);

        $this->registerPostRequest('edit', [
            'callback' => [$this, 'edit'],
        ]);

        $this->registerPostRequest('delete', [
            'callback' => [$this, 'delete'],
        ]);
    }
}
