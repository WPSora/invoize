<?php

namespace Invoize\Models\States\Interfaces;

interface RecurringStateInterface
{
  public function activate();

  public function inactivate();
}
