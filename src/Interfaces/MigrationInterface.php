<?php

namespace Invoize\Interfaces;

interface MigrationInterface
{
    public static function instance();
    public function create();
    public function getPrimaryKey();
}
