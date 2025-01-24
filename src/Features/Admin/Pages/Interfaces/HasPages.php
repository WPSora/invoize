<?php

namespace Invoize\Features\Pages\Interfaces;

interface HasPages
{
    public static function hook();

    public static function assets();

    public static function render();
}
