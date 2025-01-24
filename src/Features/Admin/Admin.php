<?php

namespace Invoize\Features\Admin;

use Invoize\Interfaces\HasPlugin;
use Invoize\Traits\InteractsWithPlugin;
use Invoize\Features\Admin\Pages\Dashboard;
use Invoize\Features\Admin\Pages\Welcome;
use Invoize\Classes\Assets;
use Invoize\Classes\Widget;
use Invoize\Classes\Reminder;
use Invoize\Api;
use Routes;
use Invoize\Crons\Concerns\HasCrons;
use Invoize\Crons\MonthlyReport;
use Invoize\InvoizePlugin;
class Admin implements HasPlugin {
    use InteractsWithPlugin, HasCrons;
    protected array $pages = [Dashboard::class, Welcome::class];

    protected array $apis = [
        Api\Settings\SettingAPI::class,
        Api\Products\ProductAPI::class,
        Api\Clients\ClientAPI::class,
        Api\Business\BusinessAPI::class,
        Api\Invoices\InvoiceAPI::class,
        Api\Receipts\ReceiptAPI::class,
        Api\Migration\MigrationAPI::class,
        Api\Download\DownloadAPI::class,
        Api\Payments\BankAPI::class
    ];

    protected array $crons = [MonthlyReport::class];

    protected array $cronSchedules = [];

    public function notices() {
        global $pagenow;
        $page = ( isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : false );
        $status = ( isset( $_GET['invoize-status'] ) ? sanitize_text_field( wp_unslash( $_GET['invoize-status'] ) ) : false );
        $action = ( isset( $_GET['from-action'] ) ? sanitize_text_field( wp_unslash( $_GET['from-action'] ) ) : false );
        if ( $pagenow == 'index.php' && $this->plugin->isUpdateAvailable() ) {
            printf( "<div class='notice notice-warning is-dismissible'>\n          <div style='display:flex;align-items:center;'>\n            <div>\n              <img src='%s' width='20' height='20'/>\n            </div>\n            <div>\n              <p>\n                There is a new version of <strong>Invoize</strong> available. <a href='%s' class='thickbox open-plugin-details-modal'>\n                  View Details</a>\n              </p>\n            </div>\n          </div>\n        </div>", esc_url( $this->plugin->getPluginUrl( 'public/invoize-icon-1.svg' ) ), esc_url( $this->plugin->getModalDetailLink() ) );
        }
        if ( !$page ) {
            return;
        }
        switch ( $page ) {
            case 'wc-orders':
                if ( $status == 'success' && $action == 'generate_invoize' ) {
                    printf( "<div class='notice notice-success is-dismissible'><p>Invoice generated successfully, <a href='%s' target='_blank'> See here </a></p></div>", 'https://google.com' );
                }
                if ( $status == 'error' && $action == 'generate_invoize' ) {
                    printf( "<div class='notice notice-error is-dismissible'><p>Invoice generation failed</p></div>" );
                }
                break;
        }
    }

    public function run() {
        $this->registerHooks();
        $this->registerApiRoutes();
        $this->registerCronSchedules();
        $this->registerCrons();
    }

    public function registerHooks() {
        $hookRegistry = $this->plugin->getHookRegistry();
        $this->registerMenu( $hookRegistry );
        $this->registerAlert( $hookRegistry );
        $this->registerWidget( $hookRegistry );
        $this->registerPublicRoute();
        $this->registerCron( $hookRegistry );
        $hookRegistry->add_filter(
            'script_loader_tag',
            [Assets::getInstance(), 'addTypeAttribute'],
            10,
            3
        );
    }

    public function registerApiRoutes() {
        $this->plugin->getHookRegistry()->addAction( 'rest_api_init', function () {
            foreach ( $this->apis as $apiClass ) {
                ( new $apiClass() )->registerRoutes();
            }
        } );
    }

    function adminMenuAction() {
        foreach ( $this->pages as $class ) {
            new $class();
        }
    }

    private function registerMenu( $hookRegistry ) {
        $hookRegistry->add_action( 'admin_menu', [$this, 'adminMenuAction'] );
        $hookRegistry->add_action( 'admin_print_styles', function () {
            echo "<style>.dashicons-invoize {background-size: 22px 22px;background-image:url(" . esc_url( $this->plugin->getPluginUrl( 'public/invoize-icon-1.svg' ) ) . ");background-position: center;background-repeat: no-repeat;}</style>";
        } );
    }

    private function registerAlert( $hookRegistry ) {
        // Add Notices / Alerts
        $hookRegistry->add_action( 'admin_notices', [$this, 'notices'] );
    }

    private function registerWidget( $hookRegistry ) {
        if ( !checkPermissionIsAllowed() ) {
            return;
        }
        // Add widget in wordpress dashboard
        $hookRegistry->add_action( 'wp_dashboard_setup', [Widget::getInstance(), 'loadWidget'] );
    }

    private function registerPublicRoute() {
        // Register public route
        // domain.com/invoize-preview?token={some_token}
        Routes::map( 'invoize-preview', function () {
            Routes::load( $this->plugin->getPluginDir() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'invoize-public-preview.php' );
        } );
        Routes::map( 'xendit-payment-success', function () {
            Routes::load( $this->plugin->getPluginDir() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'payment-confirmation.php' );
        } );
        Routes::map( 'xendit-payment-failed', function () {
            Routes::load( $this->plugin->getPluginDir() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'payment-confirmation.php' );
        } );
    }

    private function registerCron( $hookRegistry ) {
        $pluginName = InvoizePlugin::getInstance()->getSlug();
        $reminder = Reminder::init();
        $hookRegistry->add_action( "{$pluginName}_reminder_hook", [$reminder, 'run_reminder_action'] );
    }

}
