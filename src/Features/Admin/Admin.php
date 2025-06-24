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
use Invoize\Models\Client;

class Admin implements HasPlugin
{
  use InteractsWithPlugin, HasCrons;

  protected array $pages = [
    Dashboard::class,
    Welcome::class,
  ];

  protected array $apis = [
    Api\Settings\SettingAPI::class,
    Api\Products\ProductAPI::class,
    Api\Clients\ClientAPI::class,
    Api\Business\BusinessAPI::class,
    Api\Invoices\InvoiceAPI::class,
    Api\Receipts\ReceiptAPI::class,
    Api\Migration\MigrationAPI::class,
    Api\Download\DownloadAPI::class,
    Api\Payments\BankAPI::class,
    Api\Payments\PaymentAPI::class,
    Api\Misc\MiscAPI::class
  ];

  protected array $crons = [
    MonthlyReport::class
  ];

  protected array $cronSchedules = [];


  public function notices()
  {
    global $pagenow;

    if ($this->plugin->isDatabaseNeedUpdate()) {
      // Show Every Pages
      printf(
        "
        <div class='notice notice-info' style='border-radius: .7em;box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);'>
            <div style='margin-bottom: 7px'>
              <div>
                <p>
                  <strong>Invoize Database Update</strong>
                </p>
                <p>
                  We recommend backing up your database to prepare for any potential issues. Click the button below to proceed with the update.
                </p>
              </div>
              <div style='margin-bottom: 10px;display: none;' id='invoize-update-database-message'>
  
              </div>
              <div>
                <button onclick='invoize_admin_js.updateDatabase(this)' name='invoize-database-update' id='invoize-database-update' class='button button-primary'>Update database</button>
              </div>
            </div>
          </div>
        "
      );
    }

    if ($pagenow == 'index.php' && $this->plugin->isUpdateAvailable()) {
      printf(
        "<div class='notice notice-warning is-dismissible'>
          <div style='display:flex;align-items:center;'>
            <div>
              <img src='%s' width='20' height='20'/>
            </div>
            <div>
              <p>
                There is a new version of <strong>Invoize</strong> available. <a href='%s' class='thickbox open-plugin-details-modal'>
                  View Details</a>
              </p>
            </div>
          </div>
        </div>",
        esc_url($this->plugin->getPluginUrl('assets/icon.svg')),
        esc_url($this->plugin->getModalDetailLink())
      );
    }
  }


  public function run()
  {
    $this->registerHooks();
    $this->registerApiRoutes();
    $this->registerCronSchedules();
    $this->registerCrons();
  }


  public function registerHooks()
  {
    $hookRegistry = $this->plugin->getHookRegistry();
    $this->registerMenu($hookRegistry);
    $this->registerAlert($hookRegistry);
    $this->registerWidget($hookRegistry);
    $this->registerPublicRoute($hookRegistry);
    $this->registerCron($hookRegistry);
    $this->registerUpdateEmail($hookRegistry);
    $this->registerScript($hookRegistry);

    $hookRegistry->add_filter(
      'script_loader_tag',
      [Assets::getInstance(), 'addTypeAttribute'],
      10,
      3
    );
  }


  public function registerScript($hookRegistry)
  {
    $hookRegistry->add_action('admin_enqueue_scripts', [$this, 'enqueueScript']);
  }


  public function enqueueScript()
  {
    wp_enqueue_script('invoize-admin-script', $this->plugin->getPluginAssetUrl('invoize-admin.js'));
    wp_localize_script('invoize-admin-script', 'invoize_admin', [
      'site_url' => get_option('site_url'),
      'nonce_for_invoize' => wp_create_nonce()
    ]);
  }


  public function registerApiRoutes()
  {
    if (invoize()->is__premium_only()) {
      // Append Premium API
      foreach ([Api\Recurring\RecurringAPI::class, Api\Payments\PaypalAPI::class, Api\Payments\XenditAPI::class, Api\Quotation\QuotationAPI::class] as $premiumApi) {
        $this->apis[] = $premiumApi;
      }
    }
    $this->plugin
      ->getHookRegistry()
      ->addAction('rest_api_init', function () {
        foreach ($this->apis as $apiClass) {
          (new $apiClass)->registerRoutes();
        }
      });
  }


  function adminMenuAction()
  {
    foreach ($this->pages as $class) new $class;
  }


  private function registerMenu($hookRegistry)
  {
    $hookRegistry->add_action('admin_menu', [$this, 'adminMenuAction']);
    $hookRegistry->add_action('admin_enqueue_scripts', function () {
      $css = ".dashicons-invoize {background-size: 22px 22px;background-image:url(" . esc_url($this->plugin->getPluginUrl('assets/icon.svg')) . ");background-position: center;background-repeat: no-repeat;}";
      wp_add_inline_style('admin-bar', $css);
    });
  }


  private function registerAlert($hookRegistry)
  {
    // Add Notices / Alerts
    $hookRegistry->add_action('admin_notices', [$this, 'notices']);
  }


  private function registerWidget($hookRegistry)
  {
    // Add widget in wordpress dashboard
    $hookRegistry->add_action('wp_dashboard_setup', [Widget::getInstance(), 'loadWidget']);
  }


  public function registerPublicRoute($hookRegistry)
  {
    $hookRegistry->add_action('init', [$this, 'registerRoute']);
  }

  public function registerRoute()
  {
    $nonce = wp_create_nonce('invoize-preview-nonce');

    /* Quotation Preview */
    // OLD URL
    Routes::map('invoize-quotation', function () use ($nonce) {
      // Validate Nonce
      invoizeValidateNonce($nonce);
      // $token = $_GET['token'] ?? null;
      $token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : null;
      wp_redirect(home_url("invoize-quotation/$token"));
      exit;
    });

    // NEW URL
    Routes::map('invoize-quotation/:token', function ($params) use ($nonce) {
      $this->registerParam($nonce, $params['token'] ?? null);
      Routes::load(
        $this->plugin->getPluginDir() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'quotation-public-preview.php',
      );
    });

    // OLD URL, redirect to new URL
    // domain.com/invoize-preview?token={some_token}
    Routes::map('invoize-preview', function () use ($nonce) {
      // validate nonce
      invoizeValidateNonce($nonce);
      // $invToken = $_GET['invoizeToken'] ?? null;
      $invToken = isset($_GET['invoizeToken']) ? sanitize_text_field($_GET['invoizeToken']) : null;
      wp_redirect(home_url("invoize-preview/$invToken"));
      exit;
    });

    // NEW URL
    Routes::map('invoize-preview/:token', function ($params) use ($nonce) {
      $this->registerParam($nonce, $params['token'] ?? null);
      Routes::load(
        $this->plugin->getPluginDir() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'invoize-public-preview.php',
      );
    });

    Routes::map('xendit-payment-success', function () use ($nonce) {
      $this->registerParam($nonce);
      Routes::load(
        $this->plugin->getPluginDir() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'payment-confirmation.php',
      );
    });

    Routes::map('xendit-payment-failed', function () use ($nonce) {
      $this->registerParam($nonce);
      Routes::load(
        $this->plugin->getPluginDir() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'payment-confirmation.php',
      );
    });

    Routes::map('invoize-xendit-webhook', function () use ($nonce) {
      $this->registerParam($nonce);
      Routes::load(
        $this->plugin->getPluginDir() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR . 'xendit-webhook-payment.php',
      );
    });
  }

  private function registerParam($nonce, $token = null)
  {
    global $invoizeGlobalParam;
    $invoizeGlobalParam = ['nonce' => $nonce];

    if ($token) {
      $invoizeGlobalParam['token'] = $token;
    }
  }

  private function registerCron($hookRegistry)
  {
    $pluginName = InvoizePlugin::getInstance()->getSlug();

    $reminder = Reminder::init();
    $hookRegistry->add_action("{$pluginName}_reminder_hook", [$reminder, 'run_reminder_action']);

    if (invoize()->is__premium_only()) {
      $recurring = \Invoize\Classes\Recurring::init();
      $hookRegistry->add_action("{$pluginName}_recurring_hook", [$recurring, 'run_recurring_action']);
    }

    $hookRegistry->add_action("{$pluginName}_clear_log_hook", [\Invoize\Classes\Log::class, 'run_clear_log_action']);
  }

  // update ivz_client email after profile update
  private function registerUpdateEmail($hookRegistry)
  {
    $hookRegistry->add_action('profile_update', function ($userId) {
      $wcClientOnInvoize = Client::wpUserId($userId)->first();

      if (!$wcClientOnInvoize) return;

      $user = get_user_by('ID', $userId);
      if (!$user) return;

      $wcClientOnInvoize->updateEmail($user->user_email);
    });
  }
}
