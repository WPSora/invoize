<?php

namespace Invoize\Classes;

use Invoize\InvoizePlugin;
use Invoize\Models\Setting;

final class Assets
{

    protected $plugin;

    protected $manifest;

    protected $manifestPath;

    public function __construct()
    {
        global $wp_filesystem;

        $this->plugin       = InvoizePlugin::getInstance();
        $this->manifestPath = $this->plugin->getPluginDir() . 'dist' . DIRECTORY_SEPARATOR . 'manifest.json';
        $this->manifest     = json_decode($wp_filesystem->get_contents($this->manifestPath), true);
    }

    public static function getInstance()
    {
        return new self;
    }

    // add type module to script
    public function addTypeAttribute($tag, $handle, $src)
    {
        if ('invoize-admin-js' == $handle) {
            $tag = wp_get_script_tag([
                'src' => esc_url($src),
                'type' => 'module'
            ]);
        } else if ('invoize-vite-client-js' == $handle || 'invoize-vite-main-js' == $handle) {
            $tag = wp_get_script_tag([
                'src' => esc_url($src),
                'type' => 'module',
                'defer' => true,
            ]);
        }
        return $tag;
    }

    protected function getJsVars()
    {
        return [
            "version"               => $this->plugin->getVersion(),
            "base_url"              => esc_url(get_site_url()),
            "plugin_url"            => esc_url($this->plugin->getPluginUrl()),
            "api_url"               => esc_url(rest_url($this->plugin->getSlug()) . '/api'),
            "can_use_premium_code"  => invoize()->is_paying_or_trial(),
            "pricing_url"           => admin_url('admin.php?page=invoize-pricing'),
            "date_format"           => invoizeDateMomentFormat(get_option('date_format')),
            "nonce"                 => wp_create_nonce("wp_rest"),
            "need_update_database"  => $this->plugin->isDatabaseNeedUpdate(),
        ];
    }

    public function loadDevelopment()
    {
        wp_enqueue_script(
            'invoize-vite-client-js',
            'http://localhost:5173/@vite/client',
            [],
            null,
            [
                'type' => 'module',
                'crossorigin' => 'anonymous'
            ]
        );
        wp_enqueue_script(
            'invoize-vite-main-js',
            'http://localhost:5173/src/main.js',
            [],
            null
        );

        wp_localize_script(
            "invoize-vite-main-js",
            "invoize",
            $this->getJsVars()
        );

        // $settings = Setting::where('option_name', 'LIKE', 'invoize.%')->pluck('option_value', 'option_name')->mapWithKeys(function ($value, $key) {
        //     return [
        //         str_replace('invoize.', '', $key) => $value
        //     ];
        // })->toArray();

        // wp_localize_script(
        //     "invoize-vite-main-js",
        //     "invoize_settings",
        //     $settings
        // );
    }

    protected function loadProduction()
    {
        wp_enqueue_script(
            "invoize-admin-js",
            $this->plugin->getPluginUrl('dist/' . $this->manifest['src/main.js']['file']),
            [],
            null,
        );

        wp_localize_script("invoize-admin-js", "invoize", $this->getJsVars());
        foreach ($this->manifest['src/main.js']['css'] as $key => $css) {
            wp_enqueue_style(
                'invoize-admin-css-' . $key,
                $this->plugin->getPluginUrl('dist/' . $css)
            );
        }
    }

    protected function loadCss()
    {
        wp_enqueue_style('invoize-admin-css', $this->plugin->getPluginAssetUrl('invoize-admin.css'));
    }

    protected function loadScript()
    {
        wp_localize_script("invoize-admin-js", "invoize", $this->getJsVars());
    }

    protected function isDevelopmentMode()
    {
        return defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'local'
            && is_array(wp_remote_get('http://localhost:5173'));
    }

    public function load()
    {

        $this->loadCss();

        if ($this->isDevelopmentMode()) {
            $this->loadDevelopment();
        } else {
            global $wp_filesystem;

            $mainJsLocation = $this->plugin->getPluginDir() . 'dist' . DIRECTORY_SEPARATOR . $this->manifest['src/main.js']['file'];
            $content        = $wp_filesystem->get_contents($mainJsLocation);

            if (invoize()->is_premium()) {
                $toReplace = 'plugins/' . invoize()->get_slug() . '/dist/';
                if (strpos($content, $toReplace) !== false) {
                    $content = str_replace($toReplace, 'plugins/' . invoize()->get_plugin_folder_name() . '/dist/', $content);
                    $wp_filesystem->put_contents($mainJsLocation, $content);
                }
            }

            $this->loadProduction();
        }
    }
}
