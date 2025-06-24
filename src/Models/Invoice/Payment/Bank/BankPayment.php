<?php

namespace Invoize\Models\Invoice\Payment\Bank;

use Invoize\Models\Invoice\Payment\BasePayment;
use Invoize\Models\Payment;

class BankPayment extends BasePayment
{
    private int $id = 1;
    private string $detail = '';
    private string $currency = 'USD';

    public function __construct()
    {
        $this->method = Payment::BANK;
    }

    public function setContent(array $bank): self
    {
        $this->id = isset($bank['id'])
            ? (int) $bank['id'] : 1;
        $this->name = $bank['name'];
        $this->type = isset($bank['type']) && !empty($bank['type'])
            ? $bank['type'] : '';
        $this->detail = isset($bank['detail']) && !empty($bank['detail'])
            ? $bank['detail'] : '';
        $this->currency = isset($bank['currency']) && !empty($bank['currency'])
            ? $bank['currency'] : 'USD';
        return $this;
    }

    public function getContent(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'method' => $this->method,
            'type' => $this->type,
            'detail' => $this->detail,
            'currency' => $this->currency,
        ];
    }
}
