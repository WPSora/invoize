<?php

namespace Invoize\Models;

use Illuminate\Database\Eloquent\Model;

abstract class WPPost extends Model
{
    // protected $connection = 'wordpress';

    protected $table = 'posts';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'post_content',
        'post_content_filtered',
        'post_title',
        'post_excerpt',
        'post_status',
        'post_type',
        'comment_status',
        'ping_status',
        'post_password',
        'to_ping',
        'pinged',
        'post_parent',
        'menu_order',
        'guid',
        'post_date',
        'post_date_gmt',
        'post_author'
    ];

    public $timestamps = false;

    protected static function booted()
    {
        static::addGlobalScope('byPostType', function ($builder) {
            $builder->where('post_type', static::postType());
        });
    }

    public static function create(array $attributes = [])
    {
        // defaults
        $attributes['post_type'] = static::postType();
        $attributes['post_status'] = 'private';

        $postId = wp_insert_post($attributes);
        return static::find($postId);
    }

    public function getMeta(string $metaName, $default = false)
    {
        $meta = $this->metas()->where('meta_key', $metaName)->first();

        if (!$meta) {
            return $default;
        }
        return $meta->meta_value;
    }

    public function setMeta(array $metaList)
    {
        // no need to serialize because it's already serialized by the method
        foreach ($metaList as $key => $value) {
            add_post_meta($this->ID, $key, $value);
        }
    }

    public function updateMeta(array $metaList)
    {
        foreach ($metaList as $key => $value) {
            update_post_meta($this->ID, $key, $value);
        }
    }

    public function metas()
    {
        return $this->hasMany(PostMeta::class, 'post_id');
    }

    abstract public static function postType();
}
