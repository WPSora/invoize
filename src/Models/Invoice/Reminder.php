<?php

namespace Invoize\Models\Invoice;

use Carbon\Carbon;
use Invoize\Interfaces\InvoiceContentInterface;

class Reminder implements InvoiceContentInterface
{
    public int $value = 0;
    public string $interval = '';
    public ?string $dueDate = null;

    public static function instance()
    {
        return new self;
    }

    public function setDueDate(string $dueDate, string $type): self
    {
        $due = Carbon::parse($dueDate);
        if ($type == Reminders::BEFORE) {
            $this->dueDate = $due->subDays($this->value)->toDateString();
        }
        if ($type == Reminders::AFTER) {
            $this->dueDate = $due->addDays($this->value)->toDateString();
        }
        return $this;
    }

    public function setContent(string $reminder): self
    {
        $arr = explode(" ", sanitize_text_field($reminder));
        $this->value = (int) $arr[0];
        $this->interval = $arr[1];
        return $this;
    }

    public function getContent(): array
    {
        return [
            'value' => $this->value,
            'interval' => $this->interval,
            'date' => $this->dueDate,
        ];
    }
}
