<?php

namespace Invoize\Features\Admin;

use Invoize\Classes\Plugin;

class Pages
{
  public function __construct()
  {
  }

  function index()
  {
    $plugin      = Plugin::getInstance();
    $templateMgr = Plugin::getTemplateManager();

    wp_enqueue_script('adminjs', $plugin->getPluginAssetUrl('admin/admin.js'));

    return $templateMgr->display('admin/index.tpl');
  }

  function setting()
  {
  }

  function addUser()
  {
    $templateMgr = Plugin::getTemplateManager();

    return $templateMgr->display('admin/add-user.tpl');
  }
}
