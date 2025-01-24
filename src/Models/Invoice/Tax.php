<?php

namespace Invoize\Models\Invoice;

use Invoize\Interfaces\InvoiceContentInterface;

class Tax implements InvoiceContentInterface
{
    private string $name = '';
    private float $value = 0;
    private ?string $type = null;
    private ?string $description = null;

    public static function instance(): self
    {
        return new self;
    }

    public function setContent(array $tax): self
    {
        $this->name = sanitize_text_field($tax['name']);
        $this->value = (float) $tax['value'];
        $this->type = isset($tax['type'])
            ? sanitize_text_field($tax['type'])
            : null;
        $this->description = isset($tax['description'])
            ? sanitize_text_field($tax['description'])
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
