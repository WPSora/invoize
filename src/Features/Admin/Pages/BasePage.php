<?php

namespace Invoize\Features\Admin\Pages;

use Invoize\Classes\Assets;
use Invoize\InvoizePlugin;

abstract class BasePage
{
    protected $plugin;

    protected $pageTitle;

    protected $menuTitle;

    protected $capability = 'manage_options';

    protected $slug;

    public function __construct()
    {
        $this->plugin = InvoizePlugin::getInstance();

        $this->boot();
    }

    public function assets()
    {
        Assets::getInstance()->load();
    }

    public function boot()
    {
        $prefix = add_submenu_page(
            $this->plugin->getSlug(),
            $this->pageTitle,
            $this->menuTitle,
            $this->capability,
            $this->plugin->getSlug() . '-' . $this->slug,
            [$this, 'render']
        );

        add_action("admin_print_scripts-{$prefix}", [$this, 'assets']);
    }

    abstract function render();
}
