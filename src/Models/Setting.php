<?php

namespace Invoize\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Invoize\Classes\Summary\Summary;
use Invoize\InvoizePlugin;
class Setting extends Model {
    // protected $connection = 'wordpress';
    protected $table = 'options';

    protected $primaryKey = 'option_id';

    public $timestamps = false;

    protected $fillable = ['option_name', 'option_value'];

    public static function createDefaultConfig() {
        $pluginName = InvoizePlugin::getInstance()->getSlug();
        // Set default prefix
        $prefixSetting = static::key( 'invoice.prefix' )->first();
        if ( !$prefixSetting ) {
            static::create( [
                'option_name'  => $pluginName . '.invoice.prefix',
                'option_value' => '#',
            ] );
        }
        $prefixReceiptSetting = static::key( 'receipt.prefix' )->first();
        if ( !$prefixReceiptSetting ) {
            static::create( [
                'option_name'  => $pluginName . '.receipt.prefix',
                'option_value' => '#',
            ] );
        }
        // Set default invoice number
        $initNumberSetting = static::key( 'invoice.startFromNumber' )->first();
        if ( !$initNumberSetting ) {
            static::create( [
                'option_name'  => $pluginName . '.invoice.startFromNumber',
                'option_value' => 1,
            ] );
        }
        $initNumberReceiptSetting = static::key( 'receipt.startFromNumber' )->first();
        if ( !$initNumberReceiptSetting ) {
            static::create( [
                'option_name'  => $pluginName . '.receipt.startFromNumber',
                'option_value' => 1,
            ] );
        }
        // Set active currency
        $activeCurrencySetting = static::key( 'invoice.currencies' )->first();
        if ( !$activeCurrencySetting ) {
            $currency = [[
                'name'   => 'USD',
                'symbol' => '&#36;',
            ], [
                'name'   => 'IDR',
                'symbol' => 'Rp',
            ]];
            $serialized = serialize( $currency );
            static::create( [
                'option_name'  => $pluginName . '.invoice.currencies',
                'option_value' => $serialized,
            ] );
        }
        // Set default currency
        $currencySetting = static::key( 'invoice.defaultCurrency' )->first();
        if ( !$currencySetting ) {
            $currency = [
                'name'   => 'USD',
                'symbol' => '&#36;',
            ];
            $serialized = serialize( $currency );
            static::create( [
                'option_name'  => $pluginName . '.invoice.defaultCurrency',
                'option_value' => $serialized,
            ] );
        }
        // Set default Xendit currency
        $xenditCurrency = static::key( 'payment.xenditPrimaryCurrency' )->first();
        if ( !$xenditCurrency ) {
            static::create( [
                'option_name'  => $pluginName . '.payment.xenditPrimaryCurrency',
                'option_value' => 'IDR',
            ] );
        }
        // Set default due date
        $dueDateSetting = static::key( 'invoice.dueDateInterval' )->first();
        if ( !$dueDateSetting ) {
            static::create( [
                'option_name'  => $pluginName . '.invoice.dueDateInterval',
                'option_value' => '30 days',
            ] );
        }
        // Set default download paper size
        $downloadPaperSizeSetting = static::key( 'invoice.downloadPaperSize' )->first();
        if ( !$downloadPaperSizeSetting ) {
            static::create( [
                'option_name'  => $pluginName . '.invoice.downloadPaperSize',
                'option_value' => 'a4',
            ] );
        }
        // Set reminders list
        $reminderSetting = static::key( 'invoice.reminders' )->first();
        if ( !$reminderSetting ) {
            $reminders = serialize( [
                '1 day',
                '3 day',
                '7 day',
                '30 day'
            ] );
            static::create( [
                'option_name'  => $pluginName . '.invoice.reminders',
                'option_value' => $reminders,
            ] );
        }
        // set reminder group
        $reminderGroupSetting = static::key( 'invoice.reminderGroups' )->first();
        if ( !$reminderGroupSetting ) {
            $reminderGroupSetting = serialize( [[
                'name'  => 'Default Reminders',
                'value' => [
                    'before' => ['1 day', '7 day', '30 day'],
                    'after'  => ['1 day', '3 day'],
                ],
            ]] );
            static::create( [
                'option_name'  => $pluginName . '.invoice.reminderGroups',
                'option_value' => $reminderGroupSetting,
            ] );
        }
        // Set default recurring
        $recurringSetting = static::key( 'invoice.recurrings' )->first();
        if ( !$recurringSetting ) {
            $recurrings = serialize( ['1 month', '6 month', '1 year'] );
            static::create( [
                'option_name'  => $pluginName . '.invoice.recurrings',
                'option_value' => $recurrings,
            ] );
        }
        // set default paypal account type
        $paypalAccountType = static::key( 'payment.activeAutomaticPaypalType' )->first();
        if ( !$paypalAccountType ) {
            static::create( [
                'option_name'  => $pluginName . '.payment.activeAutomaticPaypalType',
                'option_value' => 'live',
            ] );
        }
        // woocommerceStatusCreate is for backward compatibility
        $woocommerceSetting = static::key( 'integration.woocommerce' )->first() || static::key( 'integration.woocommerceStatusCreate' )->first();
        if ( !$woocommerceSetting ) {
            $woocommerce = serialize( [
                'cancelOnCancelOrder' => 'false',
                'createOnNewOrder'    => 'false',
                'sendOnNewOrder'      => 'false',
                'setToPaidOn'         => 'none',
                'sendOnPaid'          => 'false',
            ] );
            static::create( [
                'option_name'  => $pluginName . '.integration.woocommerce',
                'option_value' => $woocommerce,
            ] );
        }
        // Set default email templates
        $expiredEmail = static::key( 'templates.expired_invoice' )->first();
        if ( !$expiredEmail ) {
            static::create( [
                'option_name'  => $pluginName . '.templates.expired_invoice',
                'option_value' => wp_json_encode( [
                    'name'    => 'Expired Invoice',
                    'subject' => 'Expired Invoice {{invoice_number}} from {{business_name}}',
                    'body'    => "Dear {{client_name}},\n                    \n\nThis is an automatic notification to inform you about the invoice {{invoice_number}}, which was sent to you on {{invoice_date}}, has now passed its due date and is considered expired.\n                    \n\nThank you.",
                ] ),
            ] );
        }
        $unpaidEmail = static::key( 'templates.unpaid_invoice' )->first();
        if ( !$unpaidEmail ) {
            static::create( [
                'option_name'  => $pluginName . '.templates.unpaid_invoice',
                'option_value' => wp_json_encode( [
                    'name'    => 'Unpaid Invoice',
                    'subject' => 'Unpaid Invoice {{invoice_number}} from {{business_name}}',
                    'body'    => "Dear {{client_name}},\n                    \n\nThis is an automatic notification to inform you that you have a new invoice {{invoice_number}} and waiting to be paid. This invoice will be expired on {{due_date}} so please finish the payment before the due date.\n                    \n\nThank you.",
                ] ),
            ] );
        }
        $paidEmail = static::key( 'templates.paid_invoice' )->first();
        if ( !$paidEmail ) {
            static::create( [
                'option_name'  => $pluginName . '.templates.paid_invoice',
                'option_value' => wp_json_encode( [
                    'name'    => 'Paid Invoice',
                    'subject' => 'Paid Invoice {{invoice_number}} from {{business_name}}',
                    'body'    => "Dear {{client_name}},\n                    \n\nThis is an automatic notification to inform you that we have received your payment for invoice {{invoice_number}}.\n                    \n\nThank you.",
                ] ),
            ] );
        }
        $canceledEmail = static::key( 'templates.canceled_invoice' )->first();
        if ( !$canceledEmail ) {
            static::create( [
                'option_name'  => $pluginName . '.templates.canceled_invoice',
                'option_value' => wp_json_encode( [
                    'name'    => 'Canceled Invoice',
                    'subject' => 'Canceled Invoice {{invoice_number}} from {{business_name}}',
                    'body'    => "Dear {{client_name}},\n                    \n\nThis is automatic notification to inform you about the invoice {{invoice_number}}, which was sent to you on {{invoice_date}}, has been cancelled.\n                    \n\nThis means you no longer owe the amount specified in the invoice.\n                    \nThank you for your understanding.",
                ] ),
            ] );
        }
        $reminderBeforeEmail = static::key( 'templates.reminder_before' )->first();
        if ( !$reminderBeforeEmail ) {
            static::create( [
                'option_name'  => $pluginName . '.templates.reminder_before',
                'option_value' => wp_json_encode( [
                    'name'    => 'Reminder Invoice Before Due Date',
                    'subject' => 'Reminder Invoice {{invoice_number}} from {{business_name}}',
                    'body'    => "\n                    Dear {{client_name}},\n                    \n\nThis is automatic notification to remind you about the invoice {{invoice_number}} which was sent to you on {{invoice_date}}.\n                    \n\nAs of today, the invoice remains unpaid. If you have already sent the payment, please disregard this email. If not, we kindly ask you to process the payment by the due date {{due_date}}.\n                    \n\nThank you.",
                ] ),
            ] );
        }
        $reminderAfterEmail = static::key( 'templates.reminder_after' )->first();
        if ( !$reminderAfterEmail ) {
            static::create( [
                'option_name'  => $pluginName . '.templates.reminder_after',
                'option_value' => wp_json_encode( [
                    'name'    => 'Reminder Invoice After Due Date',
                    'subject' => 'Reminder Invoice {{invoice_number}} from {{business_name}}',
                    'body'    => "\n                    Dear {{client_name}},\n                    \n\nThis is an automatic notification to remind you about then invoice {{invoice_number}} which was sent to you on {{invoice_date}} which was due for payment on {{due_date}}.\n                    \n\nAs of today, the invoice remains not paid. If you have already made the payment or if there are any issues or concerns regarding the invoice, please do not hesitate to contact us.\n                    \n\nThank you for your understanding.",
                ] ),
            ] );
        }
        $recurringEmail = static::key( 'templates.recurring_invoice' )->first();
        if ( !$recurringEmail ) {
            static::create( [
                'option_name'  => $pluginName . '.templates.recurring_invoice',
                'option_value' => wp_json_encode( [
                    'name'    => 'Recurring Invoice',
                    'subject' => 'Recurring Invoice {{invoice_number}} from {{business_name}}',
                    'body'    => "\n                    Dear {{client_name}},\n                    \n\nThis is an automatic notification to remind you about your recurring invoice {{invoice_number}} which was sent to you on {{invoice_date}}.\n                    \n\nAs per our agreement, the next payment is due on {{due_date}}. If you have already scheduled the payment, please disregard this email.\n                    \n\nThank you.",
                ] ),
            ] );
        }
        // Quotation mail templates
        $quotationCreatedEmail = static::key( 'templates.created_quotation' )->first();
        if ( !$quotationCreatedEmail ) {
            static::create( [
                'option_name'  => $pluginName . '.templates.created_quotation',
                'option_value' => wp_json_encode( [
                    'name'    => 'Created Quotation',
                    'subject' => 'New Quotation {{quotation_number}} from {{business_name}}',
                    'body'    => "Dear {{client_name}},\n\nThis is an automatic notification to inform you that a new quotation {{quotation_number}} has been created for you on {{quotation_date}}.\n\nPlease review the quotation at your earliest convenience. The quotation will be valid until {{expiry_date}}.\n\nThank you.",
                ] ),
            ] );
        }
        $quotationExpiredEmail = static::key( 'templates.expired_quotation' )->first();
        if ( !$quotationExpiredEmail ) {
            static::create( [
                'option_name'  => $pluginName . '.templates.expired_quotation',
                'option_value' => wp_json_encode( [
                    'name'    => 'Expired Quotation',
                    'subject' => 'Expired Quotation {{quotation_number}} from {{business_name}}',
                    'body'    => "Dear {{client_name}},\n\nThis is an automatic notification to inform you that the quotation {{quotation_number}}, which was sent to you on {{quotation_date}}, has now expired.\n\nIf you would like to proceed with this quotation, please contact us to request a new one.\n\nThank you for your understanding.",
                ] ),
            ] );
        }
        $quotationReminderEmail = static::key( 'templates.reminder_quotation' )->first();
        if ( !$quotationReminderEmail ) {
            static::create( [
                'option_name'  => $pluginName . '.templates.reminder_quotation',
                'option_value' => wp_json_encode( [
                    'name'    => 'Reminder Quotation',
                    'subject' => 'Reminder: Quotation {{quotation_number}} from {{business_name}}',
                    'body'    => "Dear {{client_name}},\n\nThis is a friendly reminder about the quotation {{quotation_number}} that was sent to you on {{quotation_date}}.\n\nThe quotation will expire on {{expiry_date}}. If you would like to proceed with this quotation, please respond before the expiration date.\n\nThank you.",
                ] ),
            ] );
        }
    }

    protected static function booted() : void {
        // static::saving(function ($createdModel) {
        //     if (is_array($createdModel->option_value)) {
        //         $createdModel->option_value = json_encode($createdModel->option_value);
        //     }
        // });
        // static::addGlobalScope('key', function (Builder $builder) {
        //     $builder->where('option_name', 'LIKE', InvoizePlugin::getInstance()->getSlug() . '.%');
        // });
    }

    public function scopeKey( Builder $query, string $key ) {
        return $query->where( 'option_name', InvoizePlugin::getInstance()->getSlug() . '.' . $key );
    }

    public function scopeTab( Builder $query, string $tabName ) {
        return $query->where( 'option_name', 'LIKE', InvoizePlugin::getInstance()->getSlug() . '.' . $tabName . '%' );
    }

    public function scopeSummary( Builder $query ) {
        return $query->where( 'option_name', "LIKE", InvoizePlugin::getInstance()->getSlug() . '.summary' . '%' );
    }

    public static function updateSetting( $key, $value ) : void {
        $name = InvoizePlugin::getInstance()->getSlug() . '.' . $key;
        $setting = static::where( 'option_name', $name )->firstOrNew();
        $setting->option_name = $name;
        $setting->option_value = ( is_array( $value ) ? serialize( $value ) : $value );
        $setting->save();
    }

    public static function getLogo() {
        $setting = self::key( 'bussiness.logo' )->first();
        if ( $setting ) {
            $postId = $setting->option_value;
            // Check if $postId is valid
            if ( !empty( $postId ) ) {
                // Get the attachment metadata
                $fileUrl = wp_get_attachment_url( $postId );
                if ( $fileUrl ) {
                    return esc_url( $fileUrl );
                }
            }
        }
        // Return null or handle the case where the URL is not available
        return null;
    }

    public static function getLastInvoiceNumber() : int {
        $setting = self::key( 'invoice.startFromNumber' )->first();
        if ( $setting ) {
            return $setting->option_value;
        } else {
            return 1;
        }
    }

    public static function getLastReceiptNumber() : int {
        $setting = self::key( 'receipt.startFromNumber' )->first();
        if ( $setting ) {
            return $setting->option_value;
        } else {
            return 1;
        }
    }

    public static function getDefaultBusinessId() {
        $setting = self::key( 'business.default' )->first();
        if ( $setting ) {
            return $setting->option_value;
        }
    }

    public static function getDefaultCurrency() {
        $getDefaultCurrency = Setting::key( 'invoice.defaultCurrency' )->first();
        $defaultCurrency = unserialize( $getDefaultCurrency->option_value );
        return $defaultCurrency['name'];
    }

    // This will update summary for dashboard data.
    // Should call this whenever invoice is created or duplicated.
    // ONLY used when updating payment status and NOT invoice status.
    // isUpdate param is used to check if we are creating new invoice or updating
    // invoice payment status. If creating new invoice, it mean we add new value.
    // But if updating payment status invoice, it means we modify the total by substracting
    // in one payment status and adding on other payment status.
    // public static function saveSummary(string $invoiceDate, array $currency, float $total, string $paymentStatus, bool $isUpdate)
    // {
    //     $timestamp = strtotime($invoiceDate);
    //     $month = date('M', $timestamp);
    //     $year = date('Y', $timestamp);
    //     $slug = InvoizePlugin::getInstance()->getSlug();
    //     // eg: invoize.summary.total.2024.USD
    //     $totalKey = $slug . '.summary.total.' . $year . '.' . $currency['name'];
    //     $countKey = $slug . '.summary.count.' . $year . '.' . $currency['name'];
    //     $totalSetting = static::where('option_name', $totalKey)->first();
    //     $countSetting = static::where('option_name', $countKey)->first();
    //     if (!$totalSetting) {
    //         static::create([
    //             'option_name' => $totalKey,
    //             'option_value' => serialize([$month => [$paymentStatus => $total]])
    //         ]);
    //     } else {
    //         $value = unserialize($totalSetting->option_value);
    //         if (isset($value[$month]) && isset($value[$month][$paymentStatus])) {
    //             $value[$month][$paymentStatus] = $value[$month][$paymentStatus] + $total;
    //             // subtract total from the opposite payment status
    //             if ($isUpdate && $paymentStatus == Invoice::PAID) {
    //                 $value[$month][Invoice::UNPAID] = $value[$month][Invoice::UNPAID] - $total;
    //             } else if ($isUpdate && $paymentStatus == Invoice::UNPAID) {
    //                 $value[$month][Invoice::PAID] = $value[$month][Invoice::PAID] - $total;
    //             }
    //             $totalSetting->option_value = serialize($value);
    //             $totalSetting->save();
    //         }
    //     }
    //     if (!$countSetting) {
    //         static::create([
    //             'option_name' => $countKey,
    //             'option_value' => serialize([$month => [$paymentStatus => 1]]),
    //         ]);
    //     } else {
    //         $value = unserialize($countSetting->option_value);
    //         if (isset($value[$month]) && isset($value[$month][$paymentStatus])) {
    //             $value[$month][$paymentStatus] = $value[$month][$paymentStatus] + 1;
    //             // subtract count from the opposite payment status
    //             if ($isUpdate && $paymentStatus == Invoice::PAID) {
    //                 $value[$month][Invoice::UNPAID] = $value[$month][Invoice::UNPAID] - 1;
    //             } else if ($isUpdate && $paymentStatus == Invoice::UNPAID) {
    //                 $value[$month][Invoice::PAID] = $value[$month][Invoice::PAID] - 1;
    //             }
    //             $countSetting->option_value = serialize($value);
    //             $countSetting->save();
    //         }
    //     }
    // }
    /**
     * used when trashing, canceling invoice AND restoring them.
     * This function is toggling between add or remove.
     */
    // public static function toggleSummary(string $invoiceDate, array $currency, float $total, string $paymentStatus, bool $isRemove)
    // {
    //     $timestamp = strtotime($invoiceDate);
    //     $month = date('M', $timestamp);
    //     $year = date('Y', $timestamp);
    //     $slug = InvoizePlugin::getInstance()->getSlug();
    //     // eg: invoize.summary.total.2024.USD
    //     $totalKey = $slug . '.summary.total.' . $year . '.' . $currency['name'];
    //     $countKey = $slug . '.summary.count.' . $year . '.' . $currency['name'];
    //     $totalSetting = static::where('option_name', $totalKey)->first();
    //     $countSetting = static::where('option_name', $countKey)->first();
    //     if ($totalSetting && $totalSetting->option_value) {
    //         $totalValue = unserialize($totalSetting->option_value);
    //         if (isset($totalValue[$month]) && isset($totalValue[$month][$paymentStatus])) {
    //             $isRemove
    //                 ? $totalValue[$month][$paymentStatus] -= $total
    //                 : $totalValue[$month][$paymentStatus] += $total;
    //             $totalSetting->option_value = serialize($totalValue);
    //             $totalSetting->save();
    //         }
    //     }
    //     if ($countSetting && $countSetting->option_value) {
    //         $countValue = unserialize($countSetting->option_value);
    //         if (isset($countValue[$month]) && isset($countValue[$month][$paymentStatus])) {
    //             $isRemove
    //                 ? --$countValue[$month][$paymentStatus]
    //                 : ++$countValue[$month][$paymentStatus];
    //             $countSetting->option_value = serialize($countValue);
    //             $countSetting->save();
    //         }
    //     }
    // }
    // This method is used by edit invoice. This will subtract the current summary data and add
    // the new value.
    // public static function editSummary(string  $oldInvDate, string $invDate, array $oldCurrency, array $currency, float $oldTotal, float $total)
    // {
    //     // substract the old data
    //     static::adjustSummaryData($oldInvDate, $oldCurrency, $oldTotal);
    //     // then add the new data
    //     static::adjustSummaryData($invDate, $currency, $total, true);
    // }
    // private static function adjustSummaryData($invDate, $currency, $total, $isAdd = false)
    // {
    //     $timestamp = strtotime($invDate);
    //     $month = date('M', $timestamp);
    //     $year = date('Y', $timestamp);
    //     $slug = InvoizePlugin::getInstance()->getSlug();
    //     $totalKey = $slug . '.summary.total.' . $year . '.' . $currency['name'];
    //     $countKey = $slug . '.summary.count.' . $year . '.' . $currency['name'];
    //     $totalSetting = static::where('option_name', $totalKey)->first();
    //     $countSetting = static::where('option_name', $countKey)->first();
    //     // when substracting, this setting always exist. so create only for when adding new value.
    //     if (!$totalSetting) {
    //         static::create([
    //             'option_name' => $totalKey,
    //             'option_value' => serialize([$month => [Invoice::UNPAID => $total]])
    //         ]);
    //     } else {
    //         $totalValue = unserialize($totalSetting->option_value);
    //         if (isset($totalValue[$month]) && isset($totalValue[$month][Invoice::UNPAID])) {
    //             $isAdd
    //                 ? $totalValue[$month][Invoice::UNPAID] += $total
    //                 : $totalValue[$month][Invoice::UNPAID] -= $total;
    //             $totalSetting->option_value = serialize($totalValue);
    //             $totalSetting->save();
    //         }
    //     }
    //     // when substracting, this setting always exist. so create only for when adding new value.
    //     if (!$countSetting) {
    //         static::create([
    //             'option_name' => $countKey,
    //             'option_value' => serialize([$month => [Invoice::UNPAID => 1]])
    //         ]);
    //     } else {
    //         $countValue = unserialize($countSetting->option_value);
    //         if (isset($countValue[$month]) && isset($countValue[$month][Invoice::UNPAID])) {
    //             $isAdd
    //                 ? ++$countValue[$month][Invoice::UNPAID]
    //                 : --$countValue[$month][Invoice::UNPAID];
    //             $countSetting->option_value = serialize($countValue);
    //             $countSetting->save();
    //         }
    //     }
    // }
    /** $syncType is either 'month' or 'year' */
    public static function recalculateSummary( int $month, int $year, string $syncType = Summary::MONTH_SYNC ) {
        $pluginName = InvoizePlugin::getInstance()->getSlug();
        $summary = new Summary($month, $year, $syncType);
        if ( !$summary->getCurrencies() ) {
            Setting::withoutGlobalScopes()->where( 'option_name', 'LIKE', "{$pluginName}.summary.total.{$year}.%" )->delete();
            Setting::withoutGlobalScopes()->where( 'option_name', 'LIKE', "{$pluginName}.summary.count.{$year}.%" )->delete();
        }
        foreach ( $summary->getCurrencies() as $currency ) {
            $totalSummary = Setting::key( "summary.total.{$year}.{$currency}" )->first();
            $total = $summary->getTotal( $currency );
            if ( $totalSummary && $totalSummary->option_value ) {
                $result = array_merge( unserialize( $totalSummary->option_value ), $total );
                $totalSummary->option_value = serialize( $result );
                $totalSummary->save();
            } else {
                static::create( [
                    'option_name'  => "{$pluginName}.summary.total.{$year}.{$currency}",
                    'option_value' => serialize( $total ),
                ] );
            }
            $countSummary = Setting::key( "summary.count.{$year}.{$currency}" )->firstOrNew();
            $count = $summary->getCount( $currency );
            if ( $countSummary && $countSummary->option_value ) {
                $result = array_merge( unserialize( $countSummary->option_value ), $count );
                $countSummary->option_value = serialize( $result );
                $countSummary->save();
            } else {
                static::create( [
                    'option_name'  => "{$pluginName}.summary.count.{$year}.{$currency}",
                    'option_value' => serialize( $count ),
                ] );
            }
        }
    }

    public static function getXenditKey() {
        $keyMeta = static::key( 'payment.xenditKey' )->first();
        if ( empty( $keyMeta ) || empty( $keyMeta->option_value ) ) {
            throw new \Exception('Xendit key not found', 404);
        }
        $key = ( is_serialized( $keyMeta ) ? unserialize( $keyMeta->option_value ) : $keyMeta->option_value );
        return ( is_array( $key ) ? $key[0] : $key );
    }

    public static function getXenditToken() {
        $keyMeta = static::key( 'payment.xenditToken' )->first();
        if ( empty( $keyMeta ) || empty( $keyMeta->option_value ) ) {
            throw new \Exception('Xendit token not found', 404);
        }
        $key = ( is_serialized( $keyMeta ) ? unserialize( $keyMeta->option_value ) : $keyMeta->option_value );
        return ( is_array( $key ) ? $key[0] : $key );
    }

}
