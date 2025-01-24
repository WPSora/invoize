<?php

namespace Invoize\Traits;

use Invoize\Classes\Plugin;

trait InteractsWithPlugin
{
  public $plugin;

  public function __construct(Plugin $plugin)
  {
    $this->plugin = $plugin;
  }
}
