<?php

namespace Invoize;

use Invoize\Classes\Plugin as BasePlugin;
use Invoize\Models\Invoice;
use Invoize\Models\Setting;
use Invoize\Classes\Reminder;
use Carbon\Carbon;
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

    public function setSetting( $key, $value ) {
        $key = $this->getSlug() . '.' . $key;
        return update_option( $key, $value );
    }

    public function getSetting( $key, $defaultValue = false ) {
        $key = $this->getSlug() . '.' . $key;
        return get_option( $key, $defaultValue );
    }

}
