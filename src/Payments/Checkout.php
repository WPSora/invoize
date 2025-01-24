<?php

namespace Invoize\Payments;

use Invoize\Payments\CheckoutItem;

abstract class Checkout
{
    protected ?string $orderId;
    protected ?string $invoiceToken;
    protected array $items;

    public function __construct(?string $orderId = null, ?string $invoiceToken = null)
    {
        $this->orderId = $orderId;
        $this->invoiceToken = $invoiceToken;
    }

    public static function init(?string $orderId = null, ?string $invoiceToken = null)
    {
        return new static($orderId, $invoiceToken);
    }

    public function items(array $items)
    {
        $this->items = $items;
        return $this;
    }

    protected function getItems(): array
    {
        return $this->items;
    }

    protected function getSubtotal()
    {
        return array_sum(array_map(function (CheckoutItem $items) {
            return $items->getPrice() * $items->getQuantity();
        }, $this->getItems()));
    }

    abstract public function create();
}
