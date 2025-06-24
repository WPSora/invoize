<?php

namespace Invoize\Models\Invoice;

use Invoize\Interfaces\InvoiceContentInterface;

class Currency implements InvoiceContentInterface
{
    public ?string $name = null;
    public ?string $symbol = null;

    public static function instance(): self
    {
        return new self;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setSymbol(?string $symbol = null): self
    {
        if ($symbol) {
            $this->symbol = $symbol;
        }
        return $this;
    }

    public function setContent(array $currency): self
    {
        $this->name = isset($currency['name'])
            ? sanitize_text_field($currency['name'])
            : null;
        $this->symbol = isset($currency['symbol'])
            ? sanitize_text_field($currency['symbol'])
            : null;
        return $this;
    }

    public function getContent(): array
    {
        return [
            'name' => $this->name,
            'symbol' => $this->symbol,
        ];
    }
}
