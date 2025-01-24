<?php

namespace Invoize\Models\Invoice;

use Invoize\Interfaces\InvoiceContentInterface;

class Reminders implements InvoiceContentInterface
{
    public const BEFORE = 'before';
    public const AFTER = 'after';

    private array $remindersBefore = [];
    private array $remindersAfter = [];


    public static function instance(): self
    {
        return new self;
    }


    public static function getContentFromDb(array $remindersMeta, string $dueDate): array
    {
        $before = isset($remindersMeta['before']) ? $remindersMeta['before'] : null;
        $after = isset($remindersMeta['after']) ? $remindersMeta['after'] : null;
        $reminderBefore = [];
        $reminderAfter = [];

        if (!empty($before)) {
            foreach ($before as $reminder) {
                $data = "{$reminder['value']} {$reminder['interval']}";
                $reminderBefore[] = Reminder::instance()
                    ->setContent($data)
                    ->setDueDate($dueDate, static::BEFORE)
                    ->getContent();
            }
        }

        if (!empty($after)) {
            foreach ($after as $reminder) {
                $data = "{$reminder['value']} {$reminder['interval']}";
                $reminderAfter[] = Reminder::instance()
                    ->setContent($data)
                    ->setDueDate($dueDate, static::AFTER)
                    ->getContent();
            }
        }

        return [
            'before' => $reminderBefore,
            'after' => $reminderAfter,
        ];
    }


    public function setContent(?array $reminders = null, ?string $dueDate = null): self
    {
        if (isset($reminders['before']) && !empty($reminders['before'])) {
            foreach ($reminders['before'] as $reminder) {
                if (empty($dueDate)) {
                    $this->remindersBefore[] =
                        Reminder::instance()->setContent($reminder)->getContent();
                } else {
                    $this->remindersBefore[] = Reminder::instance()
                        ->setContent($reminder)
                        ->setDueDate($dueDate, static::BEFORE)
                        ->getContent();
                }
            }
        }
        if (isset($reminders['after']) && !empty($reminders['after'])) {
            foreach ($reminders['after'] as $reminder) {
                if (empty($dueDate)) {
                    $this->remindersAfter[] =
                        Reminder::instance()->setContent($reminder)->getContent();
                } else {
                    $this->remindersAfter[] = Reminder::instance()
                        ->setContent($reminder)
                        ->setDueDate($dueDate, static::AFTER)
                        ->getContent();
                }
            }
        }
        return $this;
    }


    public function getContent(): array
    {
        return [
            'before' => $this->remindersBefore,
            'after' => $this->remindersAfter,
        ];
    }
}
