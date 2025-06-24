<?php

namespace Invoize\Api\Products;

use Invoize\Api\Api;
use Invoize\Models\Product;
use WP_REST_Request;

class ProductAPI extends Api
{

    protected string $routeName = 'product';


    public function add(WP_REST_Request $request)
    {
        $name = sanitize_text_field($request->get_param('name'));
        $desc = sanitize_textarea_field($request->get_param('description'));
        $price = (float) $request->get_param('price');
        $currency = $request->get_param('currency');
        $currencyName = sanitize_text_field($currency['name']);
        $currencySymbol = sanitize_text_field($currency['symbol']);

        if (!$name) {
            return $this->response([
                'message' => "Invalid parameter"
            ], 422);
        }

        try {
            $product = Product::create(['post_title' => $name]);
            $meta = [
                'currency' => [
                    'name' => $currencyName,
                    'symbol' => $currencySymbol,
                ],
                'description' => $desc,
                'price' => $price,
            ];
            $product->setMeta($meta);
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage()
            ], 500);
        }

        return $this->response([
            'message' => "success",
            'data' => [
                'id' => $product->ID,
                'name' => $product->post_title,
                'description' => $desc,
                'price' => $price,
                'currency' => [
                    'name' => $currencyName,
                    'symbol' => $currencySymbol,
                ],
            ],
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

        $product = Product::find($id);
        if (!$product) {
            return $this->response([
                'message' => "Product not found"
            ], 404);
        }

        wp_send_json([
            'id' => $product->ID,
            'name' => $product->post_title,
        ]);
    }


    public function update(WP_REST_Request $request)
    {
        $id = sanitize_text_field($request->get_param('id'));
        if (!$id) {
            return $this->response([
                'message' => "Invalid parameter"
            ], 422);
        }

        $product = Product::find($id);
        if (!$product) {
            return $this->response([
                'message' => 'Product not found',
            ], 404);
        }

        try {
            $params = $request->get_params();
            foreach ($params as $key => $value) {
                if ($key == 'id' || $key == 'created_by' || $key == 'created_at') {
                    continue;
                }
                if ($key == 'currency') {
                    $product->metas()->updateOrCreate(
                        ['meta_key' => $key],
                        ['meta_key' => $key, 'meta_value' => serialize([
                            'name' => sanitize_text_field($value['name']),
                            'symbol' => sanitize_text_field($value['symbol']),
                        ])],
                    );
                    continue;
                }
                if ($key == 'name') {
                    $product->post_title = sanitize_text_field($value);
                    $product->save();
                    continue;
                }
                $product->metas()->where('meta_key', sanitize_key($key))->update(
                    ['meta_value' => sanitize_text_field($value)]
                );
            }
        } catch (\Exception $e) {
            error_log($e->getCode() . ': ' . $e->getMessage());
            return $this->response(['message' => 'Failed to update. Please check error log'], $e->getCode());
        }

        return $this->response(['message' => "success"]);
    }


    public function delete(WP_REST_Request $request)
    {
        $id = sanitize_text_field($request->get_param('id'));
        if (!$id) {
            return $this->response([
                'message' => "Invalid parameter"
            ], 422);
        }

        $product = Product::find($id);
        if (!$product) {
            return $this->response([
                'message' => "Product not found"
            ], 404);
        }

        try {
            wp_delete_post($id);
            return $this->response(
                ['message' => 'Delete Product Success'],
            );
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function list(WP_REST_Request $request)
    {
        $search = sanitize_text_field($request->get_param('search'));
        $query = Product::query()->with('metas');
        if (!empty($search)) {
            $query->where('post_title', 'LIKE', '%' . $search . '%');
        }
        $page = (int) $request->get_param('page') ?: 1;
        $per_page = (int) $request->get_param('per_page') ?: 10;
        $total_items = $query->count();
        $total_pages = ceil($total_items / $per_page);
        $start_index = ($page - 1) * $per_page;

        try {
            $products = $query
                ->orderBy('post_date', 'desc')
                ->skip($start_index)
                ->take($per_page)
                ->get()
                ->map(function ($product) {
                    $metas = $product->metas;
                    $meta = [];

                    foreach ($metas as $m) {
                        $meta[$m->meta_key] = $m->meta_value;
                    }

                    return [
                        'id' => $product->ID,
                        'name' => $product->post_title,
                        'description' => isset($meta['description'])
                            ? $meta['description'] : null,
                        'price' => isset($meta['price'])
                            ? $meta['price'] : null,
                        'currency' => isset($meta['currency'])
                            ? unserialize($meta['currency']) : null,
                        'created_at' => $product->post_date,
                        'created_by' => get_user_by('id', $product->post_author)->display_name,
                    ];
                })
                // this if for woocommerce product
                // ->reject(function ($product) {
                //     return $product->name == 'AUTO-DRAFT';
                // })
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e
            ], 500);
        }

        return $this->response([
            'items' => $products,
            'page' => $page,
            'per_page' => $per_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
        ]);
    }


    public function wc_products(WP_REST_Request $request)
    {
        $search = sanitize_text_field($request->get_param('search'));
        $wcProducts = Product::query()->wcProduct();
        if (!empty($search)) {
            $wcProducts->where('post_title', 'LIKE', '%' . $search . '%');
        }
        $page = (int) $request->get_param('page') ?: 1;
        $per_page = (int) $request->get_param('per_page') ?: 10;
        $total_items = $wcProducts->count();
        $total_pages = ceil($total_items / $per_page);
        $start_index = ($page - 1) * $per_page;
        $products = [];

        try {
            $wcProducts = $wcProducts
                ->orderBy('post_date', 'desc')
                ->skip($start_index)
                ->take($per_page)
                ->get()
                ->reject(function ($product) {
                    return $product->post_title == 'AUTO-DRAFT';
                });

            $currencyName = get_woocommerce_currency();
            $currencySymbol = get_woocommerce_currency_symbol();

            foreach ($wcProducts as $p) {
                $id = $p->ID;
                $wcProduct = $p->asWcProduct();
                $name = $wcProduct->get_name();
                $price = $wcProduct->get_price();
                $product = [
                    'id' => $id,
                    'name' => $name,
                    'price' => $price,
                    'currency' => [
                        'name' => $currencyName,
                        'symbol' => $currencySymbol,
                    ]
                ];

                // for product that has variation
                if ($wcProduct->is_type('variable')) {
                    $variations = $wcProduct->get_children();
                    foreach ($variations as $variationId) {
                        $variation = wc_get_product($variationId);
                        $variationName = $variation->get_name();
                        $variationPrice = $variation->get_price();
                        $product['variation'][] = [
                            'id' => $variationId,
                            'name' => $variationName,
                            'price' => $variationPrice,
                            'currency' => [
                                'name' => $currencyName,
                                'symbol' => $currencySymbol,
                            ]
                        ];
                    }
                }
                $products[] = $product;
            }
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage(),
            ], 500);
        }

        return $this->response([
            'items' => $products,
            'page' => $page,
            'per_page' => $per_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
        ]);
    }


    public function registerRoutes()
    {

        $this->registerPostRequest('add', [
            'callback' => [$this, 'add']
        ]);

        $this->registerGetRequest('detail', [
            'callback' => [$this, 'detail'],
        ]);

        $this->registerPostRequest('delete', [
            'callback' => [$this, 'delete'],
        ]);

        $this->registerGetRequest('list', [
            'callback' => [$this, 'list'],
        ]);

        $this->registerGetRequest('wc-products', [
            'callback' => [$this, 'wc_products'],
        ]);

        $this->registerPostRequest('update', [
            'callback' => [$this, 'update'],
        ]);
    }
}
