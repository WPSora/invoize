<?php

namespace Invoize\Classes;

class HookRegistry
{
  protected $actions = [];

  protected $filters = [];

  protected $adminMenus = [];

  public function addAction($hook, $callback, $priority = 10, $accepted_args = 1)
  {
    $this->actions = $this->add($this->actions, $hook, $callback, $priority, $accepted_args);
  }

  public function add_action($hook, $callback, $priority = 10, $accepted_args = 1)
  {
    $this->actions = $this->add($this->actions, $hook, $callback, $priority, $accepted_args);
  }

  public function addFilter($hook, $callback, $priority = 10, $accepted_args = 1)
  {
    $this->filters = $this->add($this->filters, $hook, $callback, $priority, $accepted_args);
  }

  public function add_filter($hook, $callback, $priority = 10, $accepted_args = 1)
  {
    $this->addFilter($hook, $callback, $priority, $accepted_args);
  }

  private function add($hooks, $hook, $callback, $priority, $accepted_args)
  {
    $hooks[] = array(
      'hook'          => $hook,
      'callback'      => $callback,
      'priority'      => $priority,
      'accepted_args' => $accepted_args
    );

    return $hooks;
  }

  public function run()
  {
    foreach ($this->filters as $hook) {
      add_filter($hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args']);
    }

    foreach ($this->actions as $hook) {
      add_action($hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args']);
    }
  }

  public function unregister()
  {
    foreach ($this->filters as $hook) {
      remove_filter($hook['hook'], $hook['callback'], $hook['priority']);
    }

    foreach ($this->actions as $hook) {
      remove_action($hook['hook'], $hook['callback'], $hook['priority']);
    }
  }
}
