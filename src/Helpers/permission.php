<?php

function invoizeCheckPermissionIsAllowed(): bool
{
    if (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'local') {
        return true;
    }
    return current_user_can('administrator') || current_user_can('editor');
}
