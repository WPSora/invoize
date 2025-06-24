<?php

namespace Invoize\Features\Admin\Pages;


class Dashboard extends BasePage
{
    protected $pageTitle  = 'Dashboard';

    protected $menuTitle = 'Dashboard';

    protected $capability = 'manage_options';

    protected $slug = 'dashboard';


    public function boot()
    {
        $prefix =  add_menu_page(
            $this->plugin->getPluginName(),
            $this->plugin->getPluginName(),
            'manage_options',
            $this->plugin->getSlug(),
            [$this, 'render'],
            'dashicons-invoize',
            2,
        );

        add_action("admin_print_scripts-{$prefix}", [$this, 'assets']);

        $prefix = add_submenu_page(
            $this->plugin->getSlug(),
            'Dashboard',
            'Dashboard',
            'manage_options',
            $this->plugin->getSlug() . '#/',
            [$this, 'render'],
            1,
        );

        $prefix = add_submenu_page(
            $this->plugin->getSlug(),
            'Quotation',
            'Quotation',
            'manage_options',
            $this->plugin->getSlug() . '#/quotations',
            [$this, 'render'],
            2,
        );

        $prefix = add_submenu_page(
            $this->plugin->getSlug(),
            'Invoice',
            'Invoice',
            'manage_options',
            $this->plugin->getSlug() . '#/invoices',
            [$this, 'render'],
            3,
        );

        $prefix = add_submenu_page(
            $this->plugin->getSlug(),
            'Recurring',
            invoize()->can_use_premium_code() ? 'Recurring' : 'Recurring (Pro Only)',
            'manage_options',
            $this->plugin->getSlug() . '#/recurrings',
            [$this, 'render'],
            4,
        );

        $prefix = add_submenu_page(
            $this->plugin->getSlug(),
            'Receipt',
            'Receipt',
            'manage_options',
            $this->plugin->getSlug() . '#/receipts',
            [$this, 'render'],
            5,
        );
        $prefix = add_submenu_page(
            $this->plugin->getSlug(),
            'Customer',
            'Customer',
            'manage_options',
            $this->plugin->getSlug() . '#/customers',
            [$this, 'render'],
            6,
        );
        $prefix = add_submenu_page(
            $this->plugin->getSlug(),
            'Product',
            'Product',
            'manage_options',
            $this->plugin->getSlug() . '#/products',
            [$this, 'render'],
            7,
        );

        $prefix = add_submenu_page(
            $this->plugin->getSlug(),
            'Setting',
            'Setting',
            'manage_options',
            $this->plugin->getSlug() . '#/setting',
            [$this, 'render'],
            8,
        );

        // To remove first submenu, which is the default shown and it has
        // the same name as the plugin name. Renaming this submenu
        // will also change the menu. So instead we remove it and add a new
        // submenu with 'Dashboard' name
        remove_submenu_page(
            $this->plugin->getSlug(),
            $this->plugin->getSlug()
        );

        // $prefix = add_submenu_page(
        //     $this->plugin->getSlug(),
        //     'Products',
        //     'Products',
        //     'manage_options',
        //     $this->plugin->getSlug() . '#/products',
        //     [$this, 'render']
        // );

        // $prefix = add_submenu_page(
        //     $this->plugin->getSlug(),
        //     'Client',
        //     'Client',
        //     'manage_options',
        //     $this->plugin->getSlug() . '#/client',
        //     [$this, 'render']
        // );
    }
    public function render()
    {
        echo "<div class='wrap' id='invoize-app'></div>";
    }
}
