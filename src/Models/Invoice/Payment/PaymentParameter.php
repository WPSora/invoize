<?php

namespace Invoize\Models\Invoice\Payment;

class PaymentParameter
{
    private ?string $id;
    private ?string $token;
    private ?float $total;
    private ?string $currencyName;
    private ?string $dueDate;
    private ?array $customer;

    public static function instance(): self
    {
        return new self;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getCurrency()
    {
        return $this->currencyName;
    }

    public function setCurrency(string $currencyName): self
    {
        $this->currencyName = $currencyName;
        return $this;
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }

    public function setDueDate(string $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer(array $customer): self
    {
        $this->customer = $customer;
        return $this;
    }
}
