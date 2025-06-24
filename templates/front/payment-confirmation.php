<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

add_action('wp_enqueue_scripts', function () {
  global $wp_styles;
  foreach ($wp_styles->queue as $handle) {
    if (in_array($handle, ['admin-bar', 'invoize-admin-css-0'])) {
      continue;
    }
    wp_dequeue_style($handle);
    wp_deregister_style($handle);
  }
});
$custom_css = "
  @media print {
    @page {
      margin: 0;
    }
  }
";

global $params;
invoizeValidateNonce($params['nonce'] ?? null);

wp_add_inline_style('invoize-payment-confirmation', $custom_css);
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
  <div id="payment-confirmation"></div>
</body>
<?php wp_footer(); ?>

</html>