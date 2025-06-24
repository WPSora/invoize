<?php

namespace Invoize\Migration;

use Invoize\Migration\MigrationStatus;

abstract class Migration
{
    abstract protected function getBusinesses(): array;
    abstract protected function getCustomers(): array;
    abstract protected function getProducts(): array;
    abstract protected function getInvoices(): array;
    abstract protected function getRecurrings(): array;
    abstract protected function getSettings(): array;
    protected array $migrationStatus = [];

    public static function instance()
    {
        return new static;
    }

    public function run()
    {
        $migrations = [
            'Setting'   => $this->getSettings(),
            'Business'  => $this->getBusinesses(),
            'Customer'  => $this->getCustomers(),
            'Product'   => $this->getProducts(),
            'Invoice'   => $this->getInvoices(),
            'Recurring' => $this->getRecurrings(),
        ];

        foreach ($migrations as $name => $data) {
            $status = $this->migrate($name, $data);
            $this->migrationStatus[] = $status;
        }
        return $this->migrationStatus;
    }

    protected function migrate(string $name, array $migrationData)
    {
        $status = MigrationStatus::instance($name);
        foreach ($migrationData as $data) {
            try {
                $data->create();
                $status->addSuccess();
            } catch (\Exception $e) {
                $message = "$name migration failed. $name ID: {$data->getPrimaryKey()}. Message: {$e->getMessage()}";
                $status->addFailed($data->getPrimaryKey(), $message);
            }
        }
        return $status->getContent();
    }
}
