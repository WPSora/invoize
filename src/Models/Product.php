<?php

namespace Invoize\Models;

class Product extends WPPost
{
    public static function postType()
    {
        return 'ivz_product';
    }

    // get woocommerce product
    public function scopewcProduct($query)
    {
        return $query->withoutGlobalScopes()->where('post_type', 'product');
    }

    // get product from woocommerce and invoize
    public static function combined()
    {
        return static::wcProduct()->orWhere('post_type', static::postType());
    }

    public function asWcProduct()
    {
        return wc_get_product($this->ID);
    }
}
