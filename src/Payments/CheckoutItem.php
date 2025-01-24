<?php

namespace Invoize\Payments;

class CheckoutItem
{
    protected string $name;

    protected float $price;

    protected int $quantity;

    public static function instance()
    {
        return new static;
    }

    public function name(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function price(float $price)
    {
        $this->price = $price;
        return $this;
    }

    public function quantity(int $quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
