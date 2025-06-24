<?php

namespace Invoize\Classes\Summary;

use Carbon\Carbon;
use Invoize\Classes\Summary\Currencies;
use Invoize\Models\Invoice;

class Summary
{
    /** for auto sync after invoice created */
    public const MONTH_SYNC = 'month';
    /** for manual sync after user click sync in dashboard */
    public const YEAR_SYNC = 'year';

    protected $invoices;
    /** month || year */
    protected string $syncType;
    protected int $month;
    protected int $year;
    protected Currencies $currencies;


    public function __construct(int $month, int $year, string $syncType)
    {
        $this->month = $month;
        $this->year = $year;
        $this->syncType = $syncType;
        $this->currencies = new Currencies;
        $this->getInvoices();
        $this->getData();
    }


    protected function getInvoices()
    {
        if (isset($this->invoices)) {
            return $this->invoices;
        }
        if ($this->syncType == static::MONTH_SYNC) {
            $this->invoices = Invoice::summary()->month($this->month, $this->year)->get();
        }
        if ($this->syncType == static::YEAR_SYNC) {
            $this->invoices = Invoice::summary()->year($this->year)->get();
        }
    }


    protected function getData()
    {
        foreach ($this->invoices as $inv) {
            $total = $inv->metas()->where('meta_key', 'total')->value('meta_value');
            $status = $inv->metas()->where('meta_key', 'payment_status')->value('meta_value');
            $currency = $inv->metas()->where('meta_key', 'currency')->value('meta_value');
            $invDate = $inv->metas()->where('meta_key', 'invoice_date')->value('meta_value');

            if (!$total || !$status || !$currency || !$invDate) {
                continue;
            }

            $monthName = Carbon::parse($invDate)->format('M');
            $currencyName = unserialize($currency);
            $currencyName = $currencyName['name'];

            $this->currencies->setCurrenciesWithData($currencyName, $monthName, $status, $total);
        }
    }


    public function getTotal(string $currencyName)
    {
        return $this->currencies->getData($currencyName)->getMonthsWithTotal();
    }


    public function getCount(string $currencyName)
    {
        return $this->currencies->getData($currencyName)->getMonthsWithCount();
    }


    public function getCurrencies()
    {
        return $this->currencies->getCurrencies();
    }
}
