<?php

namespace Invoize\Features\Admin\Pages;

class Welcome extends BasePage
{
    protected $pageTitle  = 'Welcome';

    protected $menuTitle = 'Welcome';

    protected $capability = 'manage_options';

    protected $slug = 'welcome';

    public function boot()
    {
        $prefix =  add_menu_page(
            "Invoize Welcome",
            "Invoize Welcome",
            'manage_options',
            $this->plugin->getSlug() . '-' . $this->slug,
            [$this, 'render']
        );

        add_action("admin_print_scripts-{$prefix}", [$this, 'assets']);


        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

        add_action('admin_print_styles', function () use ($page) {
            echo "<style>li#toplevel_page_invoize-welcome {display: none;}</style>";
            if ($page == 'invoize-welcome') {
                echo "<style>#wpcontent, #wpbody, #wpwrap {background: white !important;}</style>";
            }
        });
    }

    public function render()
    {
        echo "<div class='wrap' id='invoize-app'></div>";
    }
}
