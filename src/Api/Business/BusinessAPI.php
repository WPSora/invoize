<?php

namespace Invoize\Api\Business;

use Invoize\Api\Api;
use Invoize\Models\Business;
use Invoize\Models\Setting;
use WP_REST_Request;
use Invoize\InvoizePlugin;

class BusinessAPI extends Api
{
    protected string $routeName = 'business';

    public function list(WP_REST_Request $request)
    {
        try {
            $page = (int) $request->get_param('page') ?: 1;
            $per_page = (int) $request->get_param('per_page') ?: 5;
            $start_index = ($page - 1) * $per_page;
            $total_items = Business::count();
            $total_pages = ceil($total_items / $per_page);

            $business = Business::with('metas')
                ->skip($start_index)
                ->take($per_page)
                ->get()
                ->map(function ($b) {
                    $metas = $b->metas;
                    $meta = [];

                    foreach ($metas as $m) {
                        $meta[$m->meta_key] = $m->meta_value;
                    }

                    $logo = isset($meta['logo']) ? wp_get_attachment_url((int) $meta['logo']) : null;

                    return [
                        'id' => $b->ID,
                        'business_name' => $b->post_title,
                        'phone_number' => $meta['phone_number'],
                        'email' => $meta['email'],
                        'web' => $meta['web'],
                        'address' => $meta['address'],
                        'zip' => $meta['zip'],
                        'logo' => $logo,
                    ];
                });
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage()
            ], 500);
        }

        return $this->response([
            'items' => $business,
            'page' => $page,
            'per_page' => $per_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
        ]);
    }

    /** Currently doesn't support multiple business */
    public function add(WP_REST_Request $request)
    {
        $name = sanitize_text_field($request->get_param('business_name'));
        $file = $request->get_file_params();

        if (!$name) {
            return $this->response([
                'message' => "Invalid parameter"
            ], 422);
        }

        try {
            if ($file) {
                $checkFile = Business::checkIsImageValid($file);
            }
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        try {
            $business = Business::select()->first();

            if (!$business) {
                $business = Business::create([
                    'post_title' => $name,
                ]);
            } else {
                $business->post_title = $name;
                $business->save();
            }

            foreach ($request->get_params() as $key => $value) {
                if ($key == 'business_name' || $key == 'logo') {
                    continue;
                }

                $business->metas()->updateOrCreate([
                    'meta_key' => $key,
                ], [
                    'meta_value' => $key == 'address' ? sanitize_textarea_field($value) : sanitize_text_field($value),
                ]);
            }

            if ($file) {
                require_once ABSPATH . 'wp-admin/includes/file.php';

                $uploadedFile = wp_handle_upload(
                    $file['logo'],
                    ['test_form' => FALSE]
                );
                $post_data = [
                    'post_title' => $file['logo']['name'],
                    'post_parent' => $business->getKey(),
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_author' => get_current_user_id(),
                    'post_type' => 'attachment',
                    'post_mime_type' => $checkFile['type'],
                    'guid' => $uploadedFile['url'],
                ];

                // save image to wp_post
                $uploadedLogo = wp_insert_post($post_data);

                // save image id to wp_postmeta
                $business->updateLogo($uploadedLogo);
            }

            // set as default if the first one exist
            $count = Business::count();
            if ($count == 1) {
                Setting::updateOrCreate(
                    [
                        'option_name' => InvoizePlugin::getInstance()
                            ->getSlug() . '.business.default',
                    ],
                    ['option_value' => $business->ID,]
                );
            }
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage()
            ], 500);
        }

        return $this->response([
            'message' => "success",
            'id' => $business->ID,
        ]);
    }


    public function update(WP_REST_Request $request)
    {
        $id = sanitize_text_field((int)$request->get_param('id'));
        $name = sanitize_text_field($request->get_param('business_name'));
        $logoUrl = $request->get_param('logo');
        $file = $request->get_file_params();

        $business = Business::find($id);

        if (!$business) {
            return $this->response([
                'message' => "Business not found"
            ], 404);
        }

        if (!$name) {
            return $this->response([
                'message' => "Invalid parameter"
            ], 422);
        }

        try {
            if ($file) {
                $checkFile = Business::checkIsImageValid($file);
            }
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        try {
            // update on post
            $business->post_title = $name;
            $business->save();

            // update on postmeta
            $params = $request->get_params();
            foreach ($params as $key => $value) {
                // logo will not be updated here, instead look code below this
                if ($key == 'logo') {
                    continue;
                }
                if ($key == 'address') {
                    $business->metas()->updateOrCreate(
                        ['meta_key' => $key],
                        ['meta_key' => $key, 'meta_value' => sanitize_textarea_field($value)]
                    );
                    continue;
                }
                $business->metas()->updateOrCreate(
                    ['meta_key' => $key],
                    ['meta_key' => $key, 'meta_value' => sanitize_text_field($value)]
                );
            }

            // if has logoUrl, means using old logo, so do nothing.
            // if no file or logoUrl, means we're removing the logo.
            if (!$logoUrl && !$file) {
                // delete image in wp_post
                $logoPostId = $business->metas()->where('meta_key', 'logo')->first();
                if ($logoPostId) {
                    wp_delete_post($logoPostId->meta_value);
                }
                // delete the logo from wp_postmeta
                delete_post_meta($business->ID, 'logo');

                // using new logo
            } else if ($file) {
                require_once ABSPATH . 'wp-admin/includes/file.php';

                $uploadedFile = wp_handle_upload(
                    $file['logo'],
                    ['test_form' => FALSE]
                );
                $post_data = [
                    'post_title' => $file['logo']['name'],
                    'post_parent' => $business->getKey(),
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_author' => get_current_user_id(),
                    'post_type' => 'attachment',
                    'post_mime_type' => $checkFile['type'],
                    'guid' => $uploadedFile['url'],
                ];

                // delete previous image in wp_post
                $oldImgId = $business->metas()->where('meta_key', 'logo')->first();
                if ($oldImgId) {
                    wp_delete_post($oldImgId->meta_value);
                }

                // add new image in wp_post
                $uploadedLogo = wp_insert_post($post_data);
                // save new image id to wp_postmeta
                $business->updateLogo($uploadedLogo);
            }
            // if using old logo, do nothing.
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage()
            ], 500);
        }

        return $this->response([
            'message' => 'success'
        ]);
    }


    public function delete(WP_REST_Request $request)
    {
        $id = sanitize_text_field($request->get_param('id'));

        if (!$id) {
            return $this->response([
                'message' => 'Invalid parameter'
            ], 422);
            return $this->response([
                'message' => 'Invalid parameter'
            ], 422);
        }

        $business = Business::find($id);

        if (!$business) {
            return $this->response([
                'message' => 'Business not found'
            ], 404);
        }

        try {
            // delete logo
            $logoPostId = $business->metas()->where('meta_key', 'logo')->first();
            if ($logoPostId) {
                wp_delete_post($logoPostId->meta_value);
            }
            // delete business
            wp_delete_post($id);

            // if we delete business that is default business,
            // then select other business as the default
            $defaultBusinessId = Setting::getDefaultBusinessId();
            if ($defaultBusinessId && $id == $defaultBusinessId) {
                $count = Business::count();
                if ($count > 0) {
                    // if there's business, set ID to that
                    $b = Business::first();
                    Setting::updateOrCreate(
                        [
                            'option_name' => InvoizePlugin::getInstance()
                                ->getSlug() . '.business.default'
                        ],
                        ['option_value' => $b->ID]
                    );
                } else if ($count == 0) {
                    // set to NULL if no business exist
                    Setting::updateOrCreate(
                        [
                            'option_name' => InvoizePlugin::getInstance()
                                ->getSlug() . '.business.default'
                        ],
                        ['option_value' => 0,]
                    );
                }
            }


            return $this->response(
                ['message' => 'Delete Business Success'],
            );
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function detail()
    {
        $business = Business::select()->first();

        if (!$business) {
            return wp_send_json([]);
        }

        $meta = $business->metas()
            ->get()
            ->mapWithKeys(function ($record) {
                return [$record->meta_key => $record->meta_value];
            });

        $meta = array_merge(
            ['business_name' => $business->post_title],
            $meta->toArray()
        );

        return wp_send_json(array_merge(
            ['id' => $business->getKey()],
            $meta
        ));
    }


    public function setDefault(WP_REST_Request $request)
    {
        $id = sanitize_text_field($request->get_param('id'));

        $business = Business::find($id);

        if (!$business) {
            wp_send_json([
                'message' => "Business not found"
            ], 404);
        }

        $business->makeDefault();

        wp_send_json([
            'message' => "success"
        ]);
    }


    public function registerRoutes()
    {
        $this->registerGetRequest('list', [
            'callback' => [$this, 'list']
        ]);

        $this->registerPostRequest('add', [
            'callback' => [$this, 'add']
        ]);

        $this->registerGetRequest('get', [
            'callback' => [$this, 'detail']
        ]);

        $this->registerPostRequest('update', [
            'callback' => [$this, 'update']
        ]);

        // $this->registerPostRequest('delete', [
        //     'callback' => [$this, 'delete']
        // ]);

        // $this->registerPostRequest('set-default', [
        //     'callback' => [$this, 'setDefault']
        // ]);
    }
}
