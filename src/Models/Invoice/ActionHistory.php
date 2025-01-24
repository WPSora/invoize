<?php

namespace Invoize\Models\Invoice;

use Carbon\Carbon;
use Invoize\Interfaces\InvoiceContentInterface;

class ActionHistory implements InvoiceContentInterface
{
    private ?string $from = null;
    private ?string $to = null;
    private ?string $message = null;
    private ?array $user = null;
    private string $time;

    private function  __construct()
    {
        $this->time = Carbon::now()->toDateTimeString();
    }

    public static function instance(): self
    {
        return new self;
    }

    public function setFrom(string $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function setTo(string $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function setUser(?array $user = null): self
    {
        $this->user = $user;
        return $this;
    }

    public function getContent(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'time' => $this->time,
            'message' => $this->message,
            'user' => $this->user,
        ];
    }
}
