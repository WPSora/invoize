<?php

namespace Invoize\Classes;

use Invoize\Models\Invoice;
use Invoize\Models\Quotation;
use Invoize\Models\Recurring;
class DatabaseUpdater {
    const UPDATE_TO_RUN = ['version01', 'version02'];

    public static function version01( $installedDbVersion ) {
        if ( version_compare( $installedDbVersion, '0.1', '>=' ) ) {
            return;
        }
        Invoice::chunk( 100, function ( $records ) {
            foreach ( $records as $record ) {
                $invoiceMeta = invoize_mb_unserialize( $record->getMeta( 'invoice' ) );
                if ( !$invoiceMeta ) {
                    return;
                }
                foreach ( $invoiceMeta as $key => $value ) {
                    // New name
                    if ( $key == 'invoiceDate' ) {
                        $key = 'invoice_date';
                    }
                    if ( $key == 'dueDate' ) {
                        $key = 'due_date';
                    }
                    if ( $key == 'orderDate' ) {
                        $key = 'order_date';
                    }
                    if ( $key == 'billedTo' ) {
                        $key = 'billed_to';
                    }
                    if ( $key == 'billedToSameAsClient' ) {
                        $key = 'billed_to_same_as_client';
                    }
                    $metaExists = $record->getMeta( $key, false );
                    if ( !$metaExists ) {
                        $record->setMeta( [
                            $key => $value,
                        ] );
                    }
                    if ( $metaExists ) {
                        $record->updateMeta( [
                            $key => $value,
                        ] );
                    }
                }
                $record->metas()->where( 'meta_key', 'invoice' )->delete();
            }
        } );
    }

    public static function version02( $installedDbVersion ) {
        if ( version_compare( $installedDbVersion, '0.2', '>=' ) ) {
            return;
        }
        Invoice::chunk( 100, function ( $records ) {
            foreach ( $records as $record ) {
                $recurringMeta = invoize_mb_unserialize( $record->getMeta( 'recurring' ) );
                if ( $recurringMeta ) {
                    foreach ( $recurringMeta as $key => $value ) {
                        if ( $key === 'name' ) {
                            $key = 'recurring_name';
                        }
                        if ( $key === 'start' ) {
                            $key = 'recurring_start';
                        }
                        if ( $key === 'end' ) {
                            $key = 'recurring_end_in';
                        }
                        if ( $key === 'interval' ) {
                            $key = 'recurring_interval';
                        }
                        $record->updateMeta( [
                            $key => $value,
                        ] );
                    }
                    $record->metas()->where( 'meta_key', 'recurring' )->delete();
                }
                $reminderMeta = invoize_mb_unserialize( $record->getMeta( 'reminders' ) );
                if ( $reminderMeta ) {
                    foreach ( $reminderMeta as $key => $value ) {
                        // New name
                        if ( $key == 'before' ) {
                            $key = 'reminder_before';
                        }
                        if ( $key == 'after' ) {
                            $key = 'reminder_after';
                        }
                        if ( $key == 'forClient' ) {
                            $key = 'reminder_for_client';
                        }
                        if ( $key == 'forAdmin' ) {
                            $key = 'reminder_for_admin';
                        }
                        $record->updateMeta( [
                            $key => $value,
                        ] );
                    }
                    $record->metas()->where( 'meta_key', 'reminders' )->delete();
                }
            }
        } );
    }

}
