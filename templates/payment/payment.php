<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

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
    <div id="invoize-payment"></div>
</body>
<?php wp_footer(); ?>

</html>