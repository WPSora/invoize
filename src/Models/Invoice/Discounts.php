<?php

namespace Invoize\Models\Invoice;

use Invoize\Interfaces\InvoiceContentInterface;

class Discounts implements InvoiceContentInterface
{
    private array $discounts = [];
    private float $total = 0;

    public static function instance(): self
    {
        return new self;
    }

    public function addDiscount(array $discount): self
    {
        $this->discounts[] = Discount::instance()->setContent($discount)->getContent();
        return $this;
    }

    public function setTotal($total): self
    {
        $this->total = (float) $total;
        return $this;
    }

    public function setContent(?array $discounts = null): self
    {
        if (isset($discounts['data']) && !empty($discounts['data'])) {
            foreach ($discounts['data'] as $discount) {
                $this->discounts[] = Discount::instance()->setContent($discount)->getContent();
            }
        }
        if (isset($discounts['total']) && !empty($discounts['total'])) {
            $this->total = (float) $discounts['total'];
        }
        return $this;
    }

    public function getContent(): array
    {
        return [
            'total' => $this->total,
            'data' => $this->discounts,
        ];
    }
}
