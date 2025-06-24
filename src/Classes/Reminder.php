<?php

namespace Invoize\Classes;

use Carbon\Carbon;
use Invoize\InvoizePlugin;
use Invoize\Models\Invoice;
class Reminder {
    public static function init() {
        return new self();
    }

    public function run_reminder_action() {
        $this->_run_reminder_action( Invoice::class );
    }

    public function _run_reminder_action( $model ) {
        $activeReminders = $model::with( 'metas' )->activeReminder()->get();
        if ( empty( $activeReminders ) ) {
            static::remove_reminder_schedule();
            return;
        }
        foreach ( $activeReminders as $ar ) {
            $before = $ar->metas->where( 'meta_key', 'reminder_before' )->first();
            if ( !empty( $before ) && !empty( $before->meta_value ) && !empty( unserialize( $before->meta_value ) ) ) {
                $new = $this->check_reminder(
                    unserialize( $before->meta_value ),
                    $ar->ID,
                    true,
                    $model
                );
                $before->meta_value = serialize( $new );
                $before->save();
            }
            $after = $ar->metas->where( 'meta_key', 'reminder_after' )->first();
            if ( !empty( $after ) && !empty( $after->meta_value ) && !empty( unserialize( $after->meta_value ) ) ) {
                $new = $this->check_reminder(
                    unserialize( $after->meta_value ),
                    $ar->ID,
                    false,
                    $model
                );
                $after->meta_value = serialize( $new );
                $after->save();
            }
        }
    }

    private function check_reminder(
        array $reminders,
        int $id,
        bool $isReminderBefore,
        $model = null
    ) : array {
        $model = $model ?? Invoice::class;
        $arr = [];
        foreach ( $reminders as $reminder ) {
            // if reminder has no done key, means it hasn't run yet, so run it now
            if ( isset( $reminder['done'] ) ) {
                $arr[] = $reminder;
                continue;
            }
            $data = $reminder;
            $reminderDate = Carbon::parse( $reminder['date'] );
            if ( $reminderDate->isToday() ) {
                try {
                    if ( $isReminderBefore ) {
                        Log::action( 'Reminder is running. Type: Reminder-before. ID: ' . $id );
                        $model::sendMail( $id, 'reminder-before', true );
                    } else {
                        Log::action( 'Reminder is running. Type: Reminder-after. ID: ' . $id );
                        $model::sendMail( $id, 'reminder-after', true );
                    }
                } catch ( \Exception $e ) {
                    Log::error( "Failed to send email. InvoiceID: {$id}. " . $e->getMessage() );
                }
                $data['done'] = true;
            } else {
                if ( $reminderDate->isPast() ) {
                    $data['done'] = true;
                }
            }
            $arr[] = $data;
        }
        return $arr;
    }

    public static function schedule_reminder() {
        $reminderHook = InvoizePlugin::getInstance()->getSlug() . '_reminder_hook';
        if ( !wp_next_scheduled( $reminderHook ) ) {
            // set and run cron
            wp_schedule_event( time(), 'twicedaily', $reminderHook );
            do_action( $reminderHook );
        } else {
            // only run the hook
            do_action( $reminderHook );
        }
    }

    public static function remove_reminder_schedule() {
        $reminderHook = InvoizePlugin::getInstance()->getSlug() . "_reminder_hook";
        $timestampReminder = wp_next_scheduled( $reminderHook );
        wp_unschedule_event( $timestampReminder, $reminderHook );
    }

}
