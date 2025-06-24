<?php

namespace Invoize\Models;

use Invoize\Models\WPPost;

class Debug extends WPPost
{
    public static function postType()
    {
        return "ivz_debug";
    }
}
