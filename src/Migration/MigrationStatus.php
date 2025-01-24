<?php

namespace Invoize\Migration;

class MigrationStatus
{
    protected string $name;
    protected int $success = 0; // success count
    protected array $failed = []; // [1 => 'fail message']

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function instance(string $name): self
    {
        return new self($name);
    }

    public function addSuccess(): self
    {
        $this->success = $this->success + 1;
        return $this;
    }

    public function addFailed($id, $message): self
    {
        $this->failed[$id] = $message;
        return $this;
    }

    public function getSuccess(): int
    {
        return $this->success;
    }

    public function getFailed(): array
    {
        return $this->failed;
    }

    public function getContent(): array
    {
        return [
            'name' => $this->name,
            'success' => $this->success,
            'failed' => $this->failed,
        ];
    }
}
