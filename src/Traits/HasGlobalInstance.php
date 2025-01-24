<?php

namespace Invoize\Traits;

trait HasGlobalInstance
{
  /**
   * The current globally used instance.
   *
   * @var HasGlobalInstance
   */
  protected static $instance;

  protected function setAsGlobal()
  {
    static::$instance = $this;
  }

  public static function getInstance()
  {
    return static::$instance;
  }
}
