<?php

namespace Invoize\Models\Invoice;

use Invoize\Models\Invoice\Product;
use Invoize\Interfaces\InvoiceContentInterface;

class Products implements InvoiceContentInterface
{
    public array $products = [];


    public static function instance(): self
    {
        return new self;
    }


    public function addProduct(array $product): self
    {
        $this->products[] = Product::instance()->setContent($product)->getContent();
        return $this;
    }


    public function setContent(?array $products = null): self
    {
        if (!empty($products)) {
            foreach ($products as $product) {
                $this->products[] = Product::instance()->setContent($product)->getContent();
            }
        }
        return $this;
    }


    public function getContent(): array
    {
        return $this->products;
    }
}
