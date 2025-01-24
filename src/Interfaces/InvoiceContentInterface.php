<?php

namespace Invoize\Interfaces;

use WP_User;

interface InvoiceContentInterface
{
  public static function instance();

  public function getContent();
}
