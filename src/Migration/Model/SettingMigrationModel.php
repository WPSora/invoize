<?php

namespace Invoize\Migration\Model;

use Invoize\Interfaces\MigrationInterface;
use Invoize\InvoizePlugin;

class SettingMigrationModel implements MigrationInterface
{
    private string $key = '';
    private $value;

    public static function instance(): self
    {
        return new self;
    }

    public function create()
    {
        $result = update_option(InvoizePlugin::getInstance()->getSlug() . $this->key, $this->value);
        if (!$result) {
            throw new \Exception(esc_html("Fail to add option_name: {$this->key}, option_value: {$this->value}"), 500);
        }
    }

    public function getPrimaryKey()
    {
        return 0;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
