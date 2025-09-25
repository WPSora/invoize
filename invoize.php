<?php

use Invoize\Features\Admin\Admin;
use Invoize\Features\Front\Front;
use Invoize\Features\Payment\Payment;
use Invoize\InvoizePlugin;
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Invoize
 * Plugin URI:        https://wpsora.com
 * Description:       Simplifies the process of creating, managing, and sending professional invoice
 * Version:           1.13.0
 * Author:            WP Sora
 * Author URI:        https://wpsora.com/
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 7.4
 *
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this plugin. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
if ( !function_exists( 'invoize' ) ) {
    // Create a helper function for easy SDK access.
    function invoize() {
        global $invoize;
        if ( !isset( $invoize ) ) {
            require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
            $invoize = fs_dynamic_init( array(
                'id'               => '16134',
                'slug'             => 'invoize',
                'premium_slug'     => 'invoize-pro',
                'type'             => 'plugin',
                'public_key'       => 'pk_35932ea57247ebc4c8a4329274adb',
                'is_premium'       => false,
                'has_paid_plans'   => true,
                'is_org_compliant' => true,
                'menu'             => array(
                    'slug'    => 'invoize',
                    'pricing' => true,
                ),
                'trial'            => array(
                    'days'               => 14,
                    'is_require_payment' => false,
                ),
                'is_live'          => true,
            ) );
        }
        return $invoize;
    }

    // Init Freemius.
    invoize();
    // Signal that SDK was initiated.
    do_action( 'invoize_loaded' );
}
$pluginName = 'Invoize';
$databaseVersion = 0.2;
// Database Structure Version;
$pluginVersion = get_plugin_data( __FILE__ )['Version'];
$pluginDirectory = __DIR__;
$pluginUrl = plugin_dir_url( __FILE__ );
$plugin = new InvoizePlugin(
    $pluginName,
    $pluginVersion,
    $databaseVersion,
    $pluginDirectory,
    $pluginUrl
);
$features = [Admin::class, Front::class, Payment::class];
if ( invoize_is_wc_actived() ) {
    $features[] = \Invoize\Features\Integrations\Woocommerce\Woocommerce::class;
}
$plugin->registerFeatures( $features )->run();
register_activation_hook( __FILE__, [$plugin, 'onPluginActivated'] );
register_deactivation_hook( __FILE__, [$plugin, 'onPluginDeactivated'] );