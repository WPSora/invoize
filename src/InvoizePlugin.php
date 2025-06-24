<?php

namespace Invoize;

use Invoize\Classes\Plugin as BasePlugin;
use Invoize\Models\Invoice;
use Invoize\Models\Setting;
use Invoize\Classes\Reminder;
use Carbon\Carbon;
use Exception;
use Invoize\Classes\DatabaseUpdater;
use Invoize\Classes\Log;
use Invoize\Models\Client;
class InvoizePlugin extends BasePlugin {
    public function afterConstruct() {
        // TODO: It's should be in a jobs (schedule)
        $invoices = Invoice::whereHas( 'metas', function ( $meta ) {
            $meta->where( 'meta_key', 'tab' )->where( 'meta_value', Invoice::UNPAID );
        } )->whereHas( 'metas', function ( $meta ) {
            $meta->where( 'meta_key', 'due_date' )->whereDate( 'meta_value', '<', Carbon::now() );
        } )->get();
        foreach ( $invoices as $invoice ) {
            $invoice->updateTab( Invoice::EXPIRED );
            $invoice->updateInvoiceStatus( Invoice::EXPIRED );
        }
        // TODO: This should be fired after plugin is updated.
        $setting = Setting::key( 'integration.woocommerceStatusCreate' )->first();
        $newSetting = Setting::key( 'integration.woocommerce' )->exists();
        if ( $setting && !$newSetting ) {
            $setting->update( [
                'option_name' => $this->getSlug() . '.integration.woocommerce',
            ] );
        }
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
    }

    // this function called after the plugin is done with running its features
    public function afterRun() {
    }

    /**
     * called on plugin activation
     */
    public function onPluginActivated() {
        parent::onPluginActivated();
        add_action( 'activated_plugin', [$this, 'pluginActivated'] );
    }

    public function pluginActivated() {
        Setting::createDefaultConfig();
        Reminder::schedule_reminder();
        Log::schedule_clear_log();
        Client::addRoleToWordpress();
        wp_redirect( "admin.php?page=invoize-welcome" );
        exit;
    }

    /**
     * called on plugin activation
     */
    public function onPluginDeactivated() {
        parent::onPluginDeactivated();
        Reminder::remove_reminder_schedule();
        static::getHookRegistry()->unregister();
    }

    public function getSetting( $key, $defaultValue = false ) {
        $key = $this->getSlug() . '.' . $key;
        return get_option( $key, $defaultValue );
    }

    public function isDatabaseNeedUpdate() {
        $information = invoize_mb_unserialize( invoizeGetOption( 'information', [] ) );
        if ( !isset( $information['database_version'] ) ) {
            return true;
        }
        return version_compare( $information['database_version'], $this->getDatabaseVersion(), '<' );
    }

    public function updateDatabase() {
        if ( !$this->isDatabaseNeedUpdate() ) {
            throw new Exception("Unable to update, database it's already updated");
        }
        $information = invoize_mb_unserialize( invoizeGetOption( 'information', [] ) );
        foreach ( DatabaseUpdater::UPDATE_TO_RUN as $method ) {
            if ( method_exists( DatabaseUpdater::class, $method ) ) {
                DatabaseUpdater::$method( $information['database_version'] ?? 0 );
            }
        }
        ( isset( $information['database_version'] ) ? invoizeUpdateOption( 'information', [
            'database_version' => $this->getDatabaseVersion(),
        ] ) : invoizeAddOption( 'information', [
            'database_version' => $this->getDatabaseVersion(),
        ] ) );
    }

}
