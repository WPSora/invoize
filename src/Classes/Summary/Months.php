<?php

namespace Invoize\Classes\Summary;

use Invoize\Models\Invoice;

class Months
{
    protected array $monthsWithData = []; // ['Aug' => SummaryData()]

    public function __construct(string $month, string $status, $total)
    {
        $this->updateData($month, $status, $total);
    }

    public function updateData(string $month, string $status, $total)
    {
        if (!array_key_exists($month, $this->monthsWithData)) {
            $this->monthsWithData[$month] = new SummaryData($status, $total);
        } else {
            $this->monthsWithData[$month]->updateData($status, $total);
        }
    }

    public function getMonthsWithTotal(): array
    {
        $content = [];
        foreach ($this->monthsWithData as $month => $data) {
            $content[$month] = $data->getTotal();
        }
        return $content;
    }

    public function getMonthsWithCount(): array
    {
        $content = [];
        foreach ($this->monthsWithData as $month => $data) {
            $content[$month] = $data->getCount();
        }
        return $content;
    }

    public function getMonthsList()
    {
        return array_keys($this->monthsWithData);
    }
}
