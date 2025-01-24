<?php

function checkPermissionIsAllowed(): bool
{
    if (!function_exists('wp_get_current_user')) {
        include(ABSPATH . "wp-includes/pluggable.php");
    }
    if (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'local') {
        return true;
    }
    return current_user_can('administrator') || current_user_can('editor');
}
