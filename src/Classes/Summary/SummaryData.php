<?php

namespace Invoize\Classes\Summary;

use Invoize\Models\Invoice;

class SummaryData
{
    protected float $totalUnpaid = 0;
    protected float $totalPaid = 0;
    protected int $countUnpaid = 0;
    protected int $countPaid = 0;

    public function __construct(string $status, $total)
    {
        $this->updateData($status, $total);
    }

    public function updateData(string $status, $total)
    {
        if ($status == Invoice::UNPAID) {
            $this->totalUnpaid += (float) $total;
            $this->countUnpaid += 1;
        }
        if ($status == Invoice::PAID) {
            $this->totalPaid += (float) $total;
            $this->countPaid += 1;
        }
    }

    public function getTotal()
    {
        return [
            'unpaid' => $this->totalUnpaid,
            'paid' => $this->totalPaid,
        ];
    }

    public function getCount()
    {
        return [
            'unpaid' => $this->countUnpaid,
            'paid' => $this->countPaid,
        ];
    }
}
