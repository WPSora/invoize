<?php

namespace Invoize\Api\Clients;


use Invoize\Api\Api;
use Invoize\InvoizePlugin;
use Invoize\Models\Client;
use WP_REST_Request;
use WP_User;

class ClientAPI extends Api
{

    protected string $routeName = 'client';

    public function list(WP_REST_Request $request)
    {
        $search = sanitize_text_field($request->get_param('search'));
        $query = Client::query()->with('metas');
        if (!empty($search)) {
            $query->where('post_title', 'LIKE', '%' . $search . '%');
        }
        $page = (int) $request->get_param('page') ?: 1;
        $per_page = (int) $request->get_param('per_page') ?: 10;
        $total_items = $query->count();
        $total_pages = ceil($total_items / $per_page);
        $start_index = ($page - 1) * $per_page;

        $clients = $query
            ->orderBy('post_date', 'desc')
            ->skip($start_index)
            ->take($per_page)
            ->get()
            ->map(function ($client) {
                $metas = $client->metas;
                $meta = [];
                foreach ($metas as $m) {
                    $meta[$m->meta_key] = $m->meta_value;
                }

                return [
                    'id' => $client->ID,
                    'name' => $client->getName(),
                    'created_at' => $client->post_date,
                    'email' => isset($meta['email']) ? $meta['email'] : null,
                    'phoneNumber' => isset($meta['phoneNumber']) ? $meta['phoneNumber'] : null,
                    'address' => isset($meta['address']) ? $meta['address'] : null,
                    'website' => isset($meta['website']) ? $meta['website'] : null,
                    'zip' => isset($meta['zip']) ? $meta['zip'] : null,
                    'preview_access' => isset($meta['wp_user_id']) && !empty($meta['wp_user_id'])
                        ? true : false,
                ];
            });

        return $this->response([
            'items' => $clients,
            'page' => $page,
            'per_page' => $per_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
        ]);
    }

    public function wc_clients(WP_REST_Request $request)
    {
        $search = sanitize_text_field($request->get_param('search'));
        $page = (int) $request->get_param('page') ?: 1;
        $per_page = (int) $request->get_param('per_page') ?: 10;

        try {
            $query = Client::wcCustomer(
                $page,
                $per_page,
                !empty($search) ? $search : ''
            );
            $total_items = $query->get_total();
            $items = $query->get_results();
            $total_pages = ceil($total_items / $per_page);
            $clients = array_map(function ($c) {
                $id = $c->ID;
                $name = $c->data->display_name;
                $email = $c->data->user_email;
                $metas = get_user_meta($id);
                $address = $metas['billing_address_1'][0] ?? '';
                $city = $address
                    ? ', ' . $metas['billing_city'][0] ?? ''
                    : $metas['billing_city'][0] ?? '';
                $postcode = $city
                    ? '. ' . $metas['billing_postcode'][0] ?? ''
                    : $metas['billing_postcode'][0] ?? '';

                return [
                    'id' => $id,
                    'name' => $name,
                    'email' => isset($metas['email']) && $metas['email'] ? $metas['email'] : $email,
                    'phoneNumber' => $metas['billing_phone'][0] ?? '',
                    'address' => $address . $city . $postcode,
                    'customAddress' => NULL,
                    'isWcClient' => true,
                ];
            }, $items);
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage()
            ], 500);
        }

        return $this->response([
            'items' => $clients,
            'page' => $page,
            'per_page' => $per_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
        ]);
    }


    public function detail(WP_REST_Request $request)
    {

        $id = sanitize_text_field($request->get_param('id'));
        if (!$id) {
            return $this->response([
                'message' => "Invalid parameter"
            ], 422);
        }

        $client = Client::with('metas')->find($id);
        if (!$client) {
            return $this->response([
                'message' => "Client not found"
            ], 404);
        }

        $metas = $client->metas;
        $meta = [];
        foreach ($metas as $m) {
            $meta[$m->meta_key] = $m->meta_value;
        }

        return [
            'id' => $client->ID,
            'name' => $client->getName(),
            'created_at' => $client->post_date,
            'email' => isset($meta['email']) ? $meta['email'] : null,
            'phoneNumber' => isset($meta['phoneNumber']) ? $meta['phoneNumber'] : null,
            'address' => isset($meta['address']) ? $meta['address'] : null,
            'website' => isset($meta['website']) ? $meta['website'] : null,
            'zip' => isset($meta['zip']) ? $meta['zip'] : null,
            'preview_access' => isset($meta['wp_user_id']) && !empty($meta['wp_user_id'])
                ? true : false,
        ];
    }


    public function create(WP_REST_Request $request)
    {
        $name = sanitize_text_field($request->get_param('name'));
        $email = sanitize_email($request->get_param('email'));
        // if true it means we create user in wordpress user table
        $previewAccess = filter_var($request->get_param('preview_access'), FILTER_VALIDATE_BOOL);

        if (!$name || !$email) {
            return $this->response([
                'message' => "Missing parameter"
            ], 422);
        }

        $duplicateEmail = Client::findEmail($email);
        if ($duplicateEmail) {
            return $this->response([
                'message' => 'Email already exists',
            ], 400);
        }

        try {
            $metas = [];
            foreach ($request->get_params() as $key => $value) {
                if ($key == 'name' || $key == 'id') {
                    continue;
                }
                if ($key == 'address') {
                    $metas['address'] = sanitize_textarea_field($value);
                    continue;
                }
                $metas[$key] = sanitize_text_field($value);
            }

            $client = Client::create(['post_title' => $name]);
            $wpUserId = get_user_by('email', $email);
            // no data in user table, so create one
            if ($previewAccess && !$wpUserId) {
                $wpUserId = $client->createWordpressUser($client->ID, $name, $email);
                // already has data in user table, so add role
            } else if ($previewAccess) {
                $plugin = InvoizePlugin::getInstance()->getSlug();
                $wpUserId->add_role(Client::CUSTOMER_ROLE);
                $wpUserId = $wpUserId->ID;
                add_user_meta($wpUserId, $plugin . '_client_id', $client->ID);
            }

            // add meta in postmeta
            $metas['wp_user_id'] = $wpUserId;
            $client->setMeta($metas);
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        return $this->response([
            'message' => "Success",
            'data' => [
                'id' => $client->ID,
                'name' => $client->getName(),
                'email' => isset($metas['email']) ? $metas['email'] : null,
                'phoneNumber' => isset($metas['phoneNumber']) ? $metas['phoneNumber'] : null,
                'address' => isset($metas['address']) ? $metas['address'] : null,
                'website' => isset($metas['website']) ? $metas['website'] : null,
                'zip' => isset($metas['zip']) ? $metas['zip'] : null,
                'preview_access' => $previewAccess,
            ],
        ]);
    }

    /**
     * 1. save with Preview Access off -> doesnt create User
     *    - update to on -> create User
     *    - update to off -> set Preview access to false
     *    - update to on -> set Preview access to true
     * 2. save with Preview Access on - create User
     *    - update to off -> set Preview access to false
     *    - update to on -> set Preview access to true
     */
    public function update(WP_REST_Request $request)
    {
        $id = (int) sanitize_text_field($request->get_param('id'));
        $name = sanitize_text_field($request->get_param('name'));
        $email = sanitize_email($request->get_param('email'));
        $previewAccess = filter_var($request->get_param('preview_access'), FILTER_VALIDATE_BOOL);
        if (!$id || !$name || !$email) {
            return $this->response([
                'message' => "Missing parameter"
            ], 422);
        }

        $client = Client::find($id);
        if (!$client) {
            return $this->response([
                'message' => "Client not found"
            ], 404);
        }

        $duplicateEmail = Client::findEmail($email);
        if ($duplicateEmail && $duplicateEmail->ID != $id) {
            return $this->response([
                'message' => 'Email already exists',
            ], 400);
        }

        // update ivz_client
        foreach ($request->get_params() as $key => $value) {
            if ($key == 'id' || $key == 'name' || $key == 'created_at') {
                continue;
            }
            $val = $key == 'address'
                ? sanitize_textarea_field($value)
                : sanitize_text_field($value);
            $client->metas()
                ->where('meta_key', $key)
                ->update(['meta_value' => $val]);
        }

        $client->setName($name);
        $client->save();

        // create User if not exist
        $wpUserId = $client->metas()->where('meta_key', 'wp_user_id')->value('meta_value');
        if ($previewAccess && empty($wpUserId)) {
            $wpUserId = $client->createWordpressUser($id, $name, $email);
            $client->updateMeta(['wp_user_id' => $wpUserId]);
        }

        return $this->response([
            'message' => "success"
        ]);
    }


    public function delete(WP_REST_Request $request)
    {
        $plugin = InvoizePlugin::getInstance()->getSlug();
        $id = sanitize_text_field($request->get_param('id'));
        $isDeleteWpUser = filter_var($request->get_param('isDeleteWpUser'), FILTER_VALIDATE_BOOL);
        if (!$id) {
            return $this->response([
                'message' => "Invalid parameter"
            ], 422);
        }

        $client = Client::find($id);
        if (!$client) {
            return $this->response(['message' => "Client not found"], 404);
        }

        // delete wordpress User
        $wpUserId = $client->metas()->where('meta_key', 'wp_user_id')->value('meta_value');
        try {
            if ($isDeleteWpUser && !empty($wpUserId)) {
                // delete wp User
                wp_delete_user($wpUserId);
            } else if (!$isDeleteWpUser && !empty($wpUserId)) {
                $wpUser = new WP_User($wpUserId);
                if ($wpUser) {
                    $wpUser->remove_role(Client::CUSTOMER_ROLE);
                    delete_user_meta($wpUserId, $plugin . '_client_id');
                }
            }
            // delete ivz_client user
            wp_delete_post($id);
        } catch (\Exception $e) {
            return $this->response(['message' => $e->getMessage()], $e->getCode());
        }

        return $this->response(['message' => 'Delete Client Success']);
    }


    public function registerRoutes()
    {
        $this->registerGetRequest('list', [
            'callback' => [$this, 'list'],
        ]);

        $this->registerGetRequest('wc-clients', [
            'callback' => [$this, 'wc_clients'],
        ]);

        $this->registerGetRequest('detail', [
            'callback' => [$this, 'detail'],
        ]);

        $this->registerPostRequest('create', [
            'callback' => [$this, 'create'],
        ]);

        $this->registerPostRequest('update', [
            'callback' => [$this, 'update'],
        ]);

        $this->registerPostRequest('delete', [
            'callback' => [$this, 'delete'],
        ]);
    }
}
