<?php

namespace Invoize\Models\Invoice;

use Invoize\Interfaces\InvoiceContentInterface;

class Discount implements InvoiceContentInterface
{
    public string $name = '';
    public float $value = 0;
    public ?string $type = null;
    public ?string $description = null;

    public static function instance(): self
    {
        return new self;
    }

    public function setContent(array $discount): self
    {
        $this->name = sanitize_text_field($discount['name']);
        $this->value = (float) $discount['value'];
        $this->type = isset($discount['type'])
            ? sanitize_text_field($discount['type'])
            : null;
        $this->description = isset($discount['description'])
            ? sanitize_text_field($discount['description'])
            : null;
        return $this;
    }

    public function getContent(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'value' => $this->value,
            'description' => $this->description,
        ];
    }
}
