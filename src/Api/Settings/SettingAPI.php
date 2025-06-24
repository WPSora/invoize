<?php

namespace Invoize\Api\Settings;

use WP_REST_Request;
use Invoize\Api\Api;
use Invoize\Classes\Log;
use Invoize\Models\Setting;

class SettingAPI extends Api
{
    protected string $routeName = 'settings';

    public function get_currencies()
    {
        return wp_send_json(get_woocommerce_currency_symbols());
    }

    public function get_wc_status()
    {
        return wp_send_json([
            'installed' => invoize_is_wc_actived()
        ]);
    }

    public function get_current_user()
    {
        return wp_get_current_user();
    }

    public function get_date_format()
    {
        return get_option('date_format');
    }

    public function update(WP_REST_Request $request)
    {
        $params = $request->get_params();
        $tab = $params['tab'] ?? false;

        // If 'tab' parameter exists, remove it from the array to avoid unnecessary iteration
        if ($tab) {
            unset($params['tab']);
        }

        foreach ($params as $key => $value) {
            if ($tab) {
                $key = $tab . '.' . $key; //ex: invoice.currency
            }

            Setting::updateSetting($key, $value);

            if ($key === 'other.log') {
                if (!isset($value['keepLogsFor'])) continue;

                if ($value['keepLogsFor'] !== 'forever') {
                    Log::schedule_clear_log();
                }

                if ($value['keepLogsFor'] === 'forever') {
                    Log::unschedule_clear_log();
                }
            }
        }

        return $this->response([
            'message' => "success"
        ]);
    }

    public function retrieve(WP_REST_Request $request)
    {
        $params = $request->get_params();
        $tab = $params['tab'];

        if (!$tab) {
            return $this->response([
                'message' => "Invalid parameter"
            ], 422);
        }

        $result = Setting::tab($tab)
            ->select('option_name', 'option_value')
            ->get()
            ->map(function ($item) {
                $itemValue = is_serialized($item->option_value)
                    ? invoize_mb_unserialize($item->option_value)
                    : $item->option_value;
                $itemName = substr(strrchr($item->option_name, '.'), 1);
                return [
                    'name' => $itemName,
                    'value' => $itemValue
                ];
            })
            ->toArray();

        return $this->response([
            'data' => $result
        ]);
    }

    public function findByKey(WP_REST_Request $request)
    {
        $key = sanitize_text_field($request->get_param('key'));
        $setting = Setting::key($key)->value('option_value');

        if (!$setting) {
            return $this->response([
                'message' => "Setting not found"
            ], 200);
        }


        if (in_array($setting, ['true', 'false'])) {
            $setting = $setting == 'true';
        }

        return $this->response([
            'value' => $setting
        ]);
    }

    public function defaultCurrency()
    {
        return $this->response([
            'data' => Setting::getDefaultCurrency()
        ]);
    }

    public function registerRoutes()
    {
        $this->registerGetRequest('get', [
            'callback' => [$this, 'findByKey'],
            'permission_callback' => function () {
                return invoizeCheckPermissionIsAllowed() || current_user_can('invoize_customer') || current_user_can('customer');
            }
        ]);

        $this->registerGetRequest('wc-status', [
            'callback' => [$this, 'get_wc_status']
        ]);

        $this->registerGetRequest('currencies', [
            'callback' => [$this, 'get_currencies']
        ]);

        $this->registerGetRequest('default-currency', [
            'callback' => [$this, 'defaultCurrency'],
        ]);

        $this->registerGetRequest('user', [
            'callback' => [$this, 'get_current_user']
        ]);

        $this->registerGetRequest('date-format', [
            'callback' => [$this, 'get_date_format']
        ]);

        $this->registerPostRequest('update', [
            'callback' => [$this, 'update'],
        ]);

        $this->registerPostRequest('update-log', [
            'callback' => [$this, 'updateLog'],
        ]);

        $this->registerGetRequest('retrieve', [
            'callback' => [$this, 'retrieve'],
        ]);
    }
}
