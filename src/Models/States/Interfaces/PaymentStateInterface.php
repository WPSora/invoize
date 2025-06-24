<?php

namespace Invoize\Models\States\Interfaces;

interface PaymentStateInterface
{
  public function pay();

  public function unpay();
}
