<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly
use Invoize\Models\Client;
use Invoize\Models\Invoice;

add_action('wp_enqueue_scripts', function () {
    global $wp_styles;
    foreach ($wp_styles->queue as $handle) {
        $array = [
            "admin-bar",
            "ivz-remove-wp-default-style",
            "invoize-admin-css-0",
            "dashicons",
        ];
        if (in_array($handle, $array)) {
            continue;
        }
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
});

$isValidUser = false;

if (!is_user_logged_in()) {
    auth_redirect();
}

global $params;
invoizeValidateNonce($params['nonce'] ?? null);

$token = $params['token'] ?? null;
$invoice = Invoice::whereHas('metas', function ($metas) use ($token) {
    $metas->where('meta_key', 'token')->where('meta_value', $token);
})->first();

if (!$invoice) {
    echo esc_html('Invoice not found (' . __LINE__ . ')');
    die();
}

$invoiceClientId = $invoice->metas()->where('meta_key', 'client_id')->first();
if (!$invoiceClientId) {
    echo esc_html('Invoice not found (' . __LINE__ . ')');
    die();
}
$invoiceClientId = $invoiceClientId->meta_value;

$wpUser = get_users([
    'meta_key' => 'invoize_client_id',
    'meta_value' => $invoiceClientId,
]);

$client = Client::find($invoiceClientId);
if (!$client) {
    echo esc_html('Invoice not found (' . __LINE__ . ')');
    die();
}

$hasAccess = $client->metas()->where('meta_key', 'preview_access')->first();
if ($hasAccess) {
    $hasAccess = $hasAccess->meta_value == 'false' ? false : true;
}

// deny anyone that logged in with credentials different than in the invoice
if ($hasAccess && !empty($wpUser) && $wpUser[0]->ID === get_current_user_id()) {
    $isValidUser = true;
}

$isValidUser = $isValidUser || (current_user_can('administrator') || current_user_can('editor'));

\Invoize\Classes\Assets::getInstance()->load();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php if ($isValidUser) : ?>
        <div id="invoize-preview" style="margin-bottom:20px;"></div>
    <?php else : ?>
        <div style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; padding: 20px; font-size: 20px;">
            <div>Invoice not found</div>
        </div>
    <?php endif ?>
</body>
<?php wp_footer(); ?>

</html>