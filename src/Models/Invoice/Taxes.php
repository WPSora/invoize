<?php

namespace Invoize\Models\Invoice;

use Invoize\Interfaces\InvoiceContentInterface;

class Taxes implements InvoiceContentInterface
{
    private array $taxes = [];
    private float $total = 0;

    public static function instance(): self
    {
        return new self;
    }

    public function addTax(array $tax): self
    {
        $this->taxes[] = Tax::instance()->setContent($tax)->getContent();
        return $this;
    }

    public function setTotal($total): self
    {
        $this->total = (float) $total;
        return $this;
    }

    public function setContent(?array $taxes = null): self
    {
        if (isset($taxes['data']) && !empty($taxes['data'])) {
            foreach ($taxes['data'] as $tax) {
                $this->taxes[] = Tax::instance()->setContent($tax)->getContent();
            }
        }
        if (isset($taxes['total']) && !empty($taxes['total'])) {
            $this->total = (float) $taxes['total'];
        }
        return $this;
    }

    public function getContent(): array
    {
        return [
            'total' => $this->total,
            'data' => $this->taxes,
        ];
    }
}
