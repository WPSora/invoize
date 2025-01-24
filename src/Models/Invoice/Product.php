<?php

namespace Invoize\Models\Invoice;

use Invoize\Interfaces\InvoiceContentInterface;

class Product implements InvoiceContentInterface
{
    private int $id = 0;
    private string $name = '';
    private float $unitPrice = 0;
    private int $quantity = 0;
    private float $amount = 0;
    private ?string $description = null; // won't show on invoice
    private ?string $note = null; // will show on invoice

    public static function instance(): self
    {
        return new self;
    }

    private function required(): array
    {
        // return ['id', 'name'];
        return ['name'];
    }

    public function setContent(array $product): self
    {
        foreach ($this->required() as $field) {
            if (!$product[$field]) {
                throw new \Exception(esc_html('Missing product field: ' . $field));
            }
        }
        $this->id = isset($product['id'])
            ? (int) sanitize_text_field($product['id']) : 0;
        $this->name = sanitize_text_field($product['name']);
        $this->unitPrice = (float) sanitize_text_field($product['unitPrice']);
        $this->quantity = (int) sanitize_text_field($product['quantity']);
        $this->amount = (float) sanitize_text_field($product['amount']);
        $this->description = isset($product['description'])
            ? sanitize_textarea_field($product['description']) : null;
        $this->note = isset($product['note']) && !empty($product['note'])
            ? sanitize_textarea_field($product['note']) : null;
        return $this;
    }

    public function getContent(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'unitPrice' => $this->unitPrice,
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'note' => $this->note,
        ];
    }
}
