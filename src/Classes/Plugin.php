<?php

namespace Invoize\Classes;

use Illuminate\Database\Capsule\Manager as Capsule;
use Invoize\Traits\HasGlobalInstance;

class Plugin
{


  use HasGlobalInstance;

  protected $name;

  /**
   *
   * @var string
   */
  protected $version;

  /**
   *
   * @var string
   */
  protected $databaseVersion;

  /**
   * Location of the plugin 
   *
   * @var string
   */
  protected $pluginDir;


  /**
   * URL of the plugin 
   *
   * @var string
   */
  protected $pluginUrl;

  /**
   *
   * @var array
   */
  protected $features = [];


  /**
   *
   * @var HookRegistry
   */
  public $hookRegistry;

  public $database;

  public function __construct($name, $version, $databaseVersion, $pluginDir, $pluginUrl)
  {
    $this->databaseVersion = $databaseVersion;
    $this->name = $name;
    $this->version = $version;
    $this->pluginDir = $pluginDir . DIRECTORY_SEPARATOR;
    $this->pluginUrl = $pluginUrl;
    $this->hookRegistry = new HookRegistry();

    $this->setupDatabase();
    $this->setAsGlobal();
    $this->registerFilters();
    method_exists($this, 'afterConstruct') && $this->afterConstruct();
  }

  public function registerFilters()
  {
    $this->hookRegistry->add_filter(
      'admin_footer_text',
      function () {
        return null;
      }
    );

    $this->hookRegistry->add_filter(
      'plugin_row_meta',
      function ($plugin_meta, $plugin_file) {
        if ($plugin_file == invoize()->get_plugin_basename()) {
          $links = [
            // Need help
            '<a href="https://wpsora.com/contact" target="_blank">
              <span class="dashicons dashicons-editor-help"></span>            
              Need help ?
            </a>',
            // Rate us
            '<a href="https://www.trustpilot.com/review/wpsora.com" target="_blank">
              <span class="dashicons dashicons-star-filled"></span>            
              Rate us
            </a>',
          ];
          $plugin_meta = array_merge($plugin_meta, $links);
        }
        return $plugin_meta;
      },
      10,
      2
    );
  }


  /**
   * called on plugin activation
   */
  public function onPluginActivated()
  {
    // 
  }

  /**
   * called on plugin deactivation
   */
  public function onPluginDeactivated() {}

  public function getDatabaseVersion()
  {
    return $this->databaseVersion;
  }

  public function getVersion()
  {
    return $this->version;
  }

  public function getPluginDir()
  {
    return $this->pluginDir;
  }

  public function getSlug()
  {
    return sanitize_title($this->name);
  }

  public function getPluginName()
  {
    return $this->name;
  }

  /**
   * run the Plugin
   */
  public function run()
  {
    $this->runFeatures();
    $this->runHooks();
    method_exists($this, 'afterRun') && $this->afterRun();
  }

  /**
   * Register all class that will be used
   */
  public function registerFeatures($classNames = [])
  {
    $this->features = array_merge($this->features, $classNames);

    return $this;
  }

  public function registerFeature($className)
  {
    $this->features[] = $className;

    return $this;
  }

  protected function runFeatures()
  {
    foreach ($this->features as $className) {
      $this->runFeature($className);
    }
  }

  protected function runFeature($className)
  {
    $instance = new $className($this);
    $instance->run();
  }

  protected function setupDatabase()
  {
    global $wpdb;

    $capsule = new Capsule;
    $capsule->addConnection([
      'driver' => 'mysql',
      'host' => $wpdb->dbhost,
      'database' => $wpdb->dbname,
      'username' => $wpdb->dbuser,
      'password' => $wpdb->dbpassword,
      'charset' => $wpdb->charset,
      'collation' =>  $wpdb->collate,
      'prefix' => $wpdb->prefix,
    ]);

    // Make this Capsule instance available globally via static methods... (optional)
    $capsule->setAsGlobal();

    // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
    $capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher);
    $capsule->bootEloquent();
  }

  public function getFeatures()
  {
    return $this->features;
  }

  protected function runHooks()
  {
    $this->hookRegistry->run();
  }

  public static function getHookRegistry()
  {
    $plugin = static::getInstance();
    return $plugin->hookRegistry;
  }

  public function getPluginAssetUrl($asset = '')
  {
    return $this->getPluginUrl('assets/' . $asset);
  }

  public function getPluginPath()
  {
    return $this->pluginDir;
  }

  public function getPluginUrl($path = '')
  {
    return $this->pluginUrl . $path;
  }

  public function getModalDetailLink()
  {
    return add_query_arg(
      [
        'fs_allow_updater_and_dialog' => true,
        'tab' => 'plugin-information',
        'plugin' => $this->getSlug(),
        'section' => 'changelog',
        'TB_iframe' => true,
        'width' => 772,
        'height' => 868,
      ],
      self_admin_url('plugin-install.php')
    );
  }

  public function isUpdateAvailable()
  {
    $plugins = get_site_transient('update_plugins');
    return array_key_exists(invoize()->get_plugin_basename(), $plugins->response);
  }
}
