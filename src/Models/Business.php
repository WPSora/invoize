<?php

namespace Invoize\Models;

class Business extends WPPost
{

    public static function postType()
    {
        return "ivz_business";
    }


    public function makeDefault()
    {
        $this->metas()->updateOrCreate(
            ['meta_key' => static::postType() . '.' . 'default'],
            ['meta_value' => $this->getKey()]
        );
    }


    public static function getDefault()
    {
        // return static::find(Setting::getDefaultBusinessId());
        return static::select()->first();
    }


    // postID is attachment ID in table wp_posts
    public function updateLogo($postID)
    {
        $this->metas()->updateOrCreate(
            ['meta_key' => 'logo'],
            [
                'meta_key' => 'logo',
                'meta_value' => $postID
            ]
        );
    }


    public static function checkIsImageValid($file)
    {
        $checkFile = wp_check_filetype($file['logo']['name']);

        if ($file['logo']['error']) {
            throw new \Exception("File can't be processed", 422);
        }

        if ($checkFile['type'] != 'image/jpeg' && $checkFile['type'] != 'image/png') {
            throw new \Exception("File type not allowed", 422);
        }

        return $checkFile;
    }


    public function isDefault()
    {
        return $this->metas()->where('meta_key', static::postType() . '.' . 'default')->exists();
    }
}
