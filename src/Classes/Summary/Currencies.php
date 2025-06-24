<?php

namespace Invoize\Classes\Summary;

class Currencies
{
    protected array $currenciesWithData = []; // ['USD' => Months()]

    public function setCurrenciesWithData(string $currency, string $month, string $status, $total)
    {
        if (!array_key_exists($currency, $this->currenciesWithData)) {
            $this->currenciesWithData[$currency] =  new Months($month, $status, $total);
        } else {
            $this->currenciesWithData[$currency]->updateData($month, $status, $total);
        }
    }

    public function getCurrencies(): array
    {
        return array_keys($this->currenciesWithData);
    }

    public function getData(string $currency): Months
    {
        return $this->currenciesWithData[$currency];
    }
}
