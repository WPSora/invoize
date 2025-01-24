<?php

namespace Invoize\Models;

use Carbon\Carbon;
use Invoize\InvoizePlugin;
use Invoize\Models\States\Interfaces\InvoiceStateInterface;
use Invoize\Models\States\Interfaces\PaymentStateInterface;
use Invoize\Models\States\Invoice\PaymentState\InvoicePaidState;
use Invoize\Models\States\Invoice\PaymentState\InvoiceUnpaidState;
use Invoize\Models\States\Invoice\InvoiceState\InvoiceActiveState;
use Invoize\Models\States\Invoice\InvoiceState\InvoiceExpiredState;
use Invoize\Models\States\Invoice\InvoiceState\InvoiceArchivedState;
use Invoize\Models\States\Invoice\InvoiceState\InvoiceCancelledState;
use Invoize\Models\States\Invoice\InvoiceState\InvoiceTrashedState;
use Invoize\Models\Setting;
use Invoize\Classes\Mail;
use Invoize\Classes\PDFInvoice;
use Invoize\Classes\Reminder;
use Invoize\Models\Client as ClientModel;
use Invoize\Models\Invoice\ActionHistory;
use Invoize\Models\Invoice\Business as InvoiceBusiness;
use Invoize\Models\Invoice\Client;
use Invoize\Models\Invoice\Currency;
use Invoize\Models\Invoice\Discount;
use Invoize\Models\Invoice\Discounts;
use Invoize\Models\Invoice\Payment\PaymentParameter;
use Invoize\Models\Invoice\Payment\Payments;
use Invoize\Models\Invoice\Products;
use Invoize\Models\Invoice\Reminders;
use Invoize\Models\Invoice\Tax;
use Invoize\Models\Invoice\Taxes;
use Invoize\Models\Invoice\User;
use Invoize\Payments\Constant\PaymentError;
use WC_Order_Item_Product;
use WC_Order;
class Invoice extends WPPost {
    // page
    public const INVOICE = 'invoice';

    public const RECEIPT = 'receipt';

    // payment status
    public const UNPAID = 'unpaid';

    public const PAID = 'paid';

    // invoice status
    public const ACTIVE = 'active';

    public const ARCHIVED = 'archived';

    public const CANCELLED = 'cancelled';

    public const TRASHED = 'trashed';

    public const EXPIRED = 'expired';

    public const REMINDER_BEFORE = 'reminder-before';

    public const REMINDER_AFTER = 'reminder-after';

    public const RECURRING = 'recurring';

    // this is used to check last payment state. Eg: If invoice in archived, then unarchived it, it will check this props on which tab should moved on to, either PAID or UNPAID tab.
    public $paymentStatus;

    public static function findbyOrderId( int $orderId ) {
        return static::whereHas( 'metas', function ( $meta ) use($orderId) {
            $meta->where( 'meta_key', 'wc_order_id' )->where( 'meta_value', $orderId );
        } )->first();
    }

    public static function findByToken( string $token ) {
        return static::whereHas( 'metas', function ( $meta ) use($token) {
            $meta->where( 'meta_key', 'token' )->where( 'meta_value', $token );
        } )->first();
    }

    public function getPdf() {
        $pdf = new PDFInvoice($this);
        return $pdf->generate( static::INVOICE );
    }

    public function getTaxes() {
        $taxes = $this->getMeta( 'invoice' );
        if ( !$taxes ) {
            return [];
        }
        $taxes = unserialize( $taxes );
        return $taxes['tax'];
    }

    public function getDiscounts() {
        $discounts = $this->getMeta( 'invoice' );
        if ( !$discounts ) {
            return [];
        }
        $discounts = unserialize( $discounts );
        return $discounts['discount'];
    }

    public function getTotal() {
        return $this->getMeta( 'total' );
    }

    public function getSubtotal() {
        $invoice = $this->getMeta( 'invoice' );
        if ( !$invoice ) {
            return [];
        }
        $invoice = unserialize( $invoice );
        return $invoice['subtotal'];
    }

    public function getPayments() {
        $payments = $this->getMeta( 'invoice' );
        if ( !$payments ) {
            return [];
        }
        $payments = unserialize( $payments );
        return $payments['payments'];
    }

    public function getProducts() {
        $products = $this->getMeta( 'invoice' );
        if ( !$products ) {
            return [];
        }
        $products = unserialize( $products );
        return $products['products'];
    }

    public function getCurrency() {
        return unserialize( $this->getMeta( 'currency' ) );
    }

    public function getDueDate( $usingFormat = false ) {
        if ( $usingFormat ) {
            return Carbon::parse( $this->getMeta( 'due_date' ) )->format( get_option( 'date_format' ) );
        }
        return $this->getMeta( 'due_date' );
    }

    public function getInvoiceDate() {
        return $this->getMeta( 'invoice_date' );
    }

    public function getOrderDate() {
        $invoice = $this->getMeta( 'invoice' );
        if ( !$invoice ) {
            return null;
        }
        $invoice = unserialize( $invoice );
        return $invoice['orderDate'];
    }

    public function getPaidDate() {
        return $this->getMeta( 'paid_date' );
    }

    public function getNotes() {
        $notes = $this->getMeta( 'invoice_note' );
        if ( !$notes ) {
            return '';
        }
        $notes = unserialize( $notes );
        return $notes;
    }

    public function getTerms() {
        $terms = $this->getMeta( 'invoice_note' );
        if ( !$terms ) {
            return '';
        }
        $terms = unserialize( $terms );
        return $terms['terms'];
    }

    public static function postType() {
        return "ivz_invoice";
    }

    public function getBilledTo() {
        $metas = $this->getMeta( 'invoice' );
        if ( !$metas ) {
            return [];
        }
        $metas = unserialize( $metas );
        return ( isset( $metas['billedTo'] ) ? $metas['billedTo'] : [
            'name'   => null,
            "detail" => null,
        ] );
    }

    public function isBilledToSameAsClient() {
        $meta = $this->getMeta( 'invoice' );
        return ( isset( $meta['metabilledToSameAsClient'] ) ? $meta['metabilledToSameAsClient'] : true );
    }

    public function getClient() {
        $metas = $this->getMeta( 'invoice' );
        if ( !$metas ) {
            return [];
        }
        $metas = unserialize( $metas );
        return $metas['client'];
    }

    public function getBusiness() {
        $metas = $this->getMeta( 'invoice' );
        if ( !$metas ) {
            return [];
        }
        $metas = unserialize( $metas );
        return $metas['business'];
    }

    public function getInvoiceNumber( bool $withPrefix = true ) {
        return ( $withPrefix ? $this->getMeta( 'id' ) : $this->getMeta( 'number' ) );
    }

    public function isPaymentStatusUnpaid() {
        return $this->getPaymentStatus() == static::UNPAID;
    }

    public function getPaymentStatus() {
        return $this->getMeta( 'payment_status' );
    }

    public function getType() {
        $recurring = $this->getMeta( 'recurring' );
        if ( !$recurring ) {
            return 'One-time';
        }
        return 'Recurring';
    }

    public function getToken() {
        return $this->getMeta( 'token' );
    }

    public function isRecurring() {
        return $this->getMeta( 'recurring' );
    }

    public function getMeta( string $metaName, $default = false ) {
        $meta = $this->metas()->where( 'meta_key', $metaName )->first();
        if ( !$meta ) {
            return $default;
        }
        return $meta->meta_value;
    }

    public function getPaymentState() : PaymentStateInterface {
        $state = $this->metas()->where( 'meta_key', 'payment_status' )->first();
        if ( !$state ) {
            throw new \Exception('Invalid state');
        }
        if ( $state->meta_value == static::UNPAID ) {
            $this->paymentStatus = static::UNPAID;
            return new InvoiceUnpaidState($this);
        }
        if ( $state->meta_value == static::PAID ) {
            $this->paymentStatus = static::PAID;
            return new InvoicePaidState($this);
        }
        throw new \Exception('Invalid state');
    }

    public function getInvoiceState() : InvoiceStateInterface {
        $state = $this->metas()->where( 'meta_key', 'invoice_status' )->first();
        if ( !$state ) {
            throw new \Exception('Invalid state');
        }
        if ( $state->meta_value == static::ACTIVE ) {
            return new InvoiceActiveState($this);
        }
        if ( $state->meta_value == static::EXPIRED ) {
            return new InvoiceExpiredState($this);
        }
        if ( $state->meta_value == static::CANCELLED ) {
            return new InvoiceCancelledState($this);
        }
        if ( $state->meta_value == static::ARCHIVED ) {
            return new InvoiceArchivedState($this);
        }
        if ( $state->meta_value == static::TRASHED ) {
            return new InvoiceTrashedState($this);
        }
        throw new \Exception('Invalid state');
    }

    public function duplicate() {
        $newInvoice = $this->replicate();
        $newInvoice->push();
        $invoiceDate = '';
        $currency = [];
        $total = 0;
        // if invoice is expired, then we update the due date to not expired,
        // by adding default due date from now
        $updateDueDate = false;
        $newDueDate = '';
        $invoiceStatus = $this->metas->where( 'meta_key', 'invoice_status' )->first();
        if ( $invoiceStatus && $invoiceStatus->meta_value == static::EXPIRED ) {
            $defaultDueDateSetting = Setting::key( 'invoice.dueDateInterval' )->first();
            if ( $defaultDueDateSetting && $defaultDueDateSetting->option_value ) {
                $interval = explode( " ", $defaultDueDateSetting->option_value );
                $interval = (int) $interval[0];
                $newDueDate = Carbon::now()->addDays( $interval )->toDateString();
                $updateDueDate = true;
            }
        }
        $prefix = Setting::key( 'invoice.prefix' )->first();
        $prefixValue = $prefix->option_value;
        $startFromNumber = Setting::key( 'invoice.startFromNumber' )->first();
        $startFromNumberValue = $startFromNumber->option_value;
        foreach ( $this->metas as $meta ) {
            $newMeta = $meta->replicate();
            $newMeta->post_id = $newInvoice->ID;
            if ( $newMeta->meta_key == 'invoice' && $updateDueDate ) {
                $newInvMeta = unserialize( $newMeta->meta_value );
                $newInvMeta['dueDate'] = $newDueDate;
                $newMeta->meta_value = serialize( $newInvMeta );
            }
            if ( $newMeta->meta_key == 'token' ) {
                $id = $this->metas->where( 'meta_key', 'id' )->first()->meta_value;
                $clientId = $this->metas->where( 'meta_key', 'client_id' )->first()->meta_value;
                $token = invoizeGenerateToken( $id, $clientId );
                $newMeta->meta_value = $token;
            }
            if ( $newMeta->meta_key == 'tab' ) {
                $newMeta->meta_value = Invoice::UNPAID;
            }
            if ( $newMeta->meta_key == 'payment_status' ) {
                $newMeta->meta_value = Invoice::UNPAID;
            }
            if ( $newMeta->meta_key == 'invoice_status' ) {
                $newMeta->meta_value = Invoice::ACTIVE;
            }
            if ( $newMeta->meta_key == 'id' ) {
                // update post post_title
                $newInvoice->post_title = $prefixValue . $startFromNumberValue;
                $newInvoice->save();
                // update postmeta id
                $newMeta->meta_value = $prefixValue . $startFromNumberValue;
            }
            if ( $newMeta->meta_key == 'number' ) {
                $newMeta->meta_value = $startFromNumberValue;
            }
            if ( $newMeta->meta_key == 'due_date' && $updateDueDate ) {
                $newMeta->meta_value = $newDueDate;
            }
            if ( $newMeta->meta_key == 'invoice_date' ) {
                $invoiceDate = $newMeta->meta_value;
            }
            if ( $newMeta->meta_key == 'currency' ) {
                $currency = unserialize( $newMeta->meta_value );
            }
            if ( $newMeta->meta_key == 'total' ) {
                $total = $newMeta->meta_value;
            }
            $newMeta->save();
        }
        // update options startFromNumber
        $startFromNumber->option_value = $startFromNumberValue + 1;
        $startFromNumber->save();
        $invoiceDate = Carbon::parse( $invoiceDate );
        Setting::recalculateSummary( $invoiceDate->month, $invoiceDate->year );
        return $newInvoice->ID;
    }

    public function regenerate() {
        header( 'Content-Type: text/html' );
        $newInvoice = $this->replicate();
        $newInvoice->push();
        foreach ( $this->metas as $meta ) {
            $newMeta = $meta->replicate();
            $newMeta->post_id = $newInvoice->ID;
            if ( $newMeta->meta_key == 'payments' ) {
                continue;
            }
            if ( $newMeta->meta_key == 'invoice' ) {
                $inv = invoize_mb_unserialize( $newMeta->meta_value );
                $id = $this->metas()->where( 'meta_key', 'id' )->value( 'meta_value' );
                $token = $this->getToken();
                $currency = $this->getCurrency();
                $total = $this->getTotal();
                $dueDate = $this->getDueDate();
                $client = $inv['client'];
                $inv['business'] = InvoiceBusiness::instance()->setContentByDefault()->getContent();
                $paymentParameter = PaymentParameter::instance()->setId( $id )->setToken( $token )->setCurrency( $currency['name'] )->setTotal( $total )->setDueDate( $dueDate )->setCustomer( $client );
                $newPayments = Payments::instance()->setParameter( $paymentParameter )->setContent( $inv['payments'] )->checkError()->getContent();
                // update old payment with new payment
                $inv['payments'] = $newPayments;
                $newMeta->meta_value = serialize( $inv );
                // update payments on postmeta
                Invoice::setMeta( $newInvoice->ID, [
                    'payments' => $newPayments,
                ] );
            }
            $newMeta->save();
        }
        wp_delete_post( $this->ID );
        return $newInvoice->ID;
    }

    public function updateTab( string $tab ) {
        $this->metas()->where( 'meta_key', 'tab' )->update( [
            'meta_value' => $tab,
        ] );
    }

    public function updatePaymentStatus( string $paymentStatus ) {
        $this->metas()->where( 'meta_key', 'payment_status' )->update( [
            'meta_value' => $paymentStatus,
        ] );
    }

    public function updateInvoiceStatus( string $invoiceStatus ) {
        $this->metas()->where( 'meta_key', 'invoice_status' )->update( [
            'meta_value' => $invoiceStatus,
        ] );
    }

    public function togglePaidDate( bool $isAdd ) {
        ( $isAdd ? $this->metas()->updateOrCreate( [
            'meta_key' => 'paid_date',
        ], [
            'meta_value' => Carbon::now()->toDateString(),
        ] ) : $this->metas()->where( 'meta_key', 'paid_date' )->delete() );
    }

    public function updateSummary() {
        $invoiceDate = $this->metas()->where( 'meta_key', 'invoice_date' )->value( 'meta_value' );
        $invoiceDate = Carbon::parse( $invoiceDate );
        Setting::recalculateSummary( $invoiceDate->month, $invoiceDate->year );
    }

    public function updateReceiptStatus( string $status ) {
        if ( $this->receipt && $this->paymentStatus == Invoice::PAID ) {
            $this->receipt->post_excerpt = $status;
            $this->receipt->save();
        }
    }

    public function saveActionHistory( string $from, string $to, string $message ) {
        $history = $this->metas()->where( 'meta_key', 'action_history' )->first();
        $user = User::instance()->setContent( wp_get_current_user() )->getContent();
        $actionHistory = ActionHistory::instance()->setUser( $user )->setFrom( $from )->setTo( $to )->setMessage( $user['name'] . ' has ' . $message )->getContent();
        if ( !$history ) {
            $meta = [
                'action_history' => [$actionHistory],
            ];
            $this->setMeta( $this->invoice->ID, $meta );
        } else {
            $arr = unserialize( $history->meta_value );
            $arr[] = $actionHistory;
            $history->meta_value = serialize( $arr );
            $history->save();
        }
    }

    public function updateWcOrderToComplete( bool $isSendEmail ) {
        $wcOrderId = $this->metas()->where( 'meta_key', 'wc_order_id' )->value( 'meta_value' );
        if ( !$wcOrderId ) {
            return;
        }
        $order = new WC_Order($wcOrderId);
        $order->update_status( "completed" );
        $wcOptions = Setting::key( 'integration.woocommerce' )->value( 'option_value' );
        if ( !$wcOptions ) {
            return;
        }
        $wcOptions = unserialize( $wcOptions );
        $sendOnPaid = $wcOptions['sendOnPaid'];
        // whether to send email if from setting is true or send email by trigger_pay_and_send in AddGenerateInvoiceAction
        if ( $sendOnPaid == 'true' || $isSendEmail ) {
            try {
                Invoice::sendMail( $this->ID, Invoice::PAID );
            } catch ( \Exception $e ) {
                error_log( "Failed to send email. InvoiceID: {$this->ID}. WoocommerceOrderID: {$wcOrderId}. Message: {$e->getMessage()}" );
            }
        }
    }

    public function receipt() {
        return $this->hasOne( Receipt::class, 'post_content' );
    }

    public function scopeRecurring( $query ) {
        return $query->whereHas( 'metas', function ( $meta ) {
            $meta->where( 'meta_key', 'recurring' )->where( 'meta_value', static::RECURRING );
        } );
    }

    public function scopeThisMonth( $query ) {
        return $query->whereHas( 'metas', function ( $meta ) {
            $startOfThisMonth = Carbon::today()->startOfMonth()->toDateString();
            $endOfThisMonth = Carbon::today()->endOfMonth()->toDateString();
            $meta->where( 'meta_key', 'invoice_date' )->whereBetween( 'meta_value', [$startOfThisMonth, $endOfThisMonth] );
        } );
    }

    public function scopeLastMonth( $query ) {
        return $query->whereHas( 'metas', function ( $meta ) {
            $startOfLastMonth = Carbon::today()->startOfMonth()->subMonth()->toDateString();
            $endOfLastMonth = Carbon::today()->endOfMonth()->subMonth()->toDateString();
            $meta->where( 'meta_key', 'invoice_date' )->whereBetween( 'meta_value', [$startOfLastMonth, $endOfLastMonth] );
        } );
    }

    public function scopePaid( $query ) {
        return $query->whereHas( 'metas', function ( $meta ) {
            $meta->where( 'meta_key', 'payment_status' )->where( 'meta_value', static::PAID );
        } );
    }

    public function scopeUnpaid( $query ) {
        return $query->whereHas( 'metas', function ( $meta ) {
            $meta->where( 'meta_key', 'payment_status' )->where( 'meta_value', static::UNPAID );
        } );
    }

    public function scopeUnpaidArchive( $query ) {
        return $query->whereHas( 'metas', function ( $metas ) {
            $metas->where( 'meta_key', 'payment_status' )->where( 'meta_value', static::UNPAID );
        } )->whereHas( 'metas', function ( $metas ) {
            $metas->where( 'meta_key', 'tab' )->whereIn( 'meta_value', [static::UNPAID, static::ARCHIVED] );
        } );
    }

    public function scopeActive( $query ) {
        return $query->whereHas( 'metas', function ( $meta ) {
            $meta->where( 'meta_key', 'invoice_status' )->where( 'meta_value', static::ACTIVE );
        } );
    }

    public function scopeActiveReminder( $query ) {
        return $query->whereHas( 'metas', function ( $metas ) {
            $metas->where( 'meta_key', 'payment_status' )->where( 'meta_value', Invoice::UNPAID );
        } )->where( function ( $query ) {
            $query->whereHas( 'metas', function ( $metas ) {
                $metas->where( 'meta_key', 'reminder_before' )->whereNotNull( 'meta_value' );
            } )->orWhereHas( 'metas', function ( $metas ) {
                $metas->where( 'meta_key', 'reminder_after' )->whereNotNull( 'meta_value' );
            } );
        } );
    }

    public function scopeExpired( $query ) {
        return $query->whereHas( 'metas', function ( $meta ) {
            $meta->where( 'meta_key', 'invoice_status' )->where( 'meta_value', static::EXPIRED );
        } );
    }

    public function scopeExpiredSoon( $query, int $expiredInDays = 7 ) {
        return $query->unpaid()->active()->whereHas( 'metas', function ( $meta ) use($expiredInDays) {
            $today = Carbon::today()->toDateString();
            $expiredDate = Carbon::today()->addDays( $expiredInDays )->toDateString();
            $meta->where( 'meta_key', 'due_date' )->whereBetween( 'meta_value', [$today, $expiredDate] );
        } );
    }

    public function scopeSummary( $query ) {
        return $query->whereHas( 'metas', function ( $metas ) {
            $metas->where( 'meta_key', 'invoice_status' )->whereNotIn( 'meta_value', [Invoice::CANCELLED, Invoice::TRASHED] );
        } );
    }

    public function scopeMonth( $query, int $month, int $year ) {
        return $query->whereHas( 'metas', function ( $metas ) use($month, $year) {
            $metas->where( 'meta_key', 'invoice_date' )->whereMonth( 'meta_value', $month )->whereYear( 'meta_value', $year );
        } );
    }

    public function scopeYear( $query, int $year ) {
        return $query->whereHas( 'metas', function ( $metas ) use($year) {
            $metas->where( 'meta_key', 'invoice_date' )->whereYear( 'meta_value', $year );
        } );
    }

    public function calculate() {
        return $this->metas->where( 'meta_key', 'total' )->sum( 'meta_value' );
    }

    public static function getExpiredSoon( int $limit = 10, int $expiredInDays = 7, bool $formatTotal = false ) {
        return static::expiredSoon( $expiredInDays )->take( $limit )->get()->map( function ( $inv ) use($formatTotal) {
            $number = $inv->metas()->where( 'meta_key', 'number' )->value( 'meta_value' );
            $dueDate = $inv->metas()->where( 'meta_key', 'due_date' )->value( 'meta_value' );
            $token = $inv->metas()->where( 'meta_key', 'token' )->value( 'meta_value' );
            $total = $inv->metas()->where( 'meta_key', 'total' )->value( 'meta_value' );
            $currencyMeta = $inv->metas()->where( 'meta_key', 'currency' )->value( 'meta_value' );
            $number = ( $number ? "#{$number}" : $inv->post_title );
            $currency = unserialize( $currencyMeta );
            $total = ( $formatTotal ? ( $currency['name'] == 'IDR' ? number_format(
                $total,
                0,
                ',',
                '.'
            ) : number_format(
                $total,
                0,
                '.',
                ','
            ) ) : (float) $total );
            return [
                'id'       => $inv->ID,
                'name'     => $number . ' ' . $inv->post_content,
                'dueDate'  => $dueDate,
                'token'    => $token,
                'total'    => $total,
                'currency' => $currency['symbol'],
            ];
        } );
    }

    public static function getSummary() {
        // $summaryUnpaidThisMonth = static::getSummaryFromInvoices(Invoice::UNPAID);
        // $summaryPaidThisMonth = static::getSummaryFromInvoices(Invoice::PAID);
        $summaries = Setting::summary()->get();
        $result = [];
        foreach ( $summaries as $summary ) {
            $arr = explode( '.', $summary->option_name );
            $label = $arr[2];
            $year = $arr[3];
            $currency = $arr[4];
            $result[$currency][$label][$year] = invoize_mb_unserialize( $summary->option_value );
        }
        return $result;
    }

    public static function getUnpaidCount() {
        return static::whereHas( 'metas', function ( $meta ) {
            $meta->where( 'meta_key', 'payment_status' )->where( 'meta_value', static::UNPAID )->count();
        } );
    }

    public static function getPaidCount() {
        return static::whereHas( 'metas', function ( $meta ) {
            $meta->where( 'meta_key', 'payment_status' )->where( 'meta_value', static::PAID )->count();
        } );
    }

    public static function getInvoiceCount( array $tabs ) {
        $tabsCount = [];
        foreach ( $tabs as $tab ) {
            $count = static::whereHas( 'metas', function ( $meta ) use($tab) {
                $meta->where( 'meta_key', 'tab' )->where( 'meta_value', $tab );
            } )->count();
            $tabsCount[$tab] = $count;
        }
        return $tabsCount;
    }

    public static function generateInvoice( array $invoice, $isUpdateStartNumber = true ) : int {
        $ivzClient = ClientModel::find( $invoice['client']['id'] );
        $client = $invoice['client'];
        // if not exist mean the id is wc customer id.
        if ( !$ivzClient ) {
            $client = ClientModel::updateFromWcCustomer( $invoice['client'] );
        }
        $invoiceMeta = [
            'id'              => $invoice['id'],
            'prefix'          => $invoice['prefix'],
            'number'          => $invoice['number'],
            'token'           => $invoice['token'],
            'invoice'         => [
                'business'             => $invoice['business'],
                'client'               => $client,
                'billedTo'             => $invoice['billedTo'],
                'billedToSameAsClient' => $invoice['billedToSameAsClient'],
                'status'               => $invoice['paymentStatus'],
                'orderDate'            => $invoice['orderDate'],
                'invoiceDate'          => $invoice['invoiceDate'],
                'dueDate'              => $invoice['dueDate'],
                'products'             => $invoice['products'],
                'payments'             => $invoice['payments'],
                'currency'             => $invoice['currency'],
                'subtotal'             => $invoice['subtotal'],
                'total'                => $invoice['total'],
                'discount'             => $invoice['discounts'],
                'tax'                  => $invoice['taxes'],
                'reminders'            => [
                    'before' => ( isset( $invoice['reminders'] ) && isset( $invoice['reminders']['before'] ) && !empty( $invoice['reminders']['before'] ) ? $invoice['reminders']['before'] : [] ),
                    'after'  => ( isset( $invoice['reminders'] ) && isset( $invoice['reminders']['after'] ) && !empty( $invoice['reminders']['after'] ) ? $invoice['reminders']['after'] : [] ),
                ],
            ],
            'payment_status'  => $invoice['paymentStatus'],
            'invoice_status'  => $invoice['invoiceStatus'] ?? Invoice::ACTIVE,
            'recurring'       => $invoice['recurring'] ?? NULL,
            'due_date'        => $invoice['dueDate'],
            'invoice_date'    => $invoice['invoiceDate'],
            'paid_date'       => ( $invoice['paymentStatus'] == Invoice::PAID ? $invoice['invoiceDate'] : null ),
            'total'           => $invoice['total'],
            'tab'             => $invoice['tab'] ?? $invoice['paymentStatus'],
            'user'            => $invoice['user'],
            'client_id'       => $client['id'] ?? NULL,
            'business_id'     => $invoice['business']['id'] ?? NULL,
            'recurring_id'    => $invoice['recurringId'] ?? NULL,
            'payments'        => $invoice['payments'],
            'currency'        => $invoice['currency'],
            'action_history'  => [$invoice['actionHistory']],
            'wc_order_id'     => $invoice['wc_order_id'] ?? NULL,
            'is_email_sent'   => $invoice['is_email_sent'] ?? NULL,
            'invoice_note'    => [
                'note'         => $invoice['note'] ?? NULL,
                'terms'        => $invoice['terms'] ?? NULL,
                'internalNote' => $invoice['internalNote'] ?? NULL,
            ],
            'reminder_before' => ( isset( $invoice['reminders'] ) && isset( $invoice['reminders']['before'] ) && !empty( $invoice['reminders']['before'] ) ? $invoice['reminders']['before'] : NULL ),
            'reminder_after'  => ( isset( $invoice['reminders'] ) && !empty( $invoice['reminders']['after'] ) && !empty( $invoice['reminders']['after'] ) ? $invoice['reminders']['after'] : NULL ),
        ];
        // save to wp_posts
        $inv = Invoice::create( [
            'post_title'   => $invoice['id'],
            'post_content' => $invoice['client']['name'],
        ] );
        // save to wp_postmeta
        try {
            Invoice::setMeta( $inv->ID, $invoiceMeta );
        } catch ( \Exception $e ) {
            wp_delete_post( $inv->ID );
            throw new \Exception(esc_html( 'Failed to save to postmeta. Message: ' . $e->getMessage() ), 500);
        }
        $invoiceDate = Carbon::parse( $invoice['invoiceDate'] );
        Setting::recalculateSummary( $invoiceDate->month, $invoiceDate->year );
        if ( $isUpdateStartNumber ) {
            Setting::updateSetting( 'invoice.startFromNumber', Setting::getLastInvoiceNumber() + 1 );
        }
        // static::scheduleReminder();
        Reminder::schedule_reminder();
        return $inv->ID;
    }

    public static function sendMail( $invoiceId, $status, $isFromCron = false ) {
        $meta = [];
        $invoice = static::with( 'metas' )->find( $invoiceId );
        foreach ( $invoice->metas as $m ) {
            $meta[$m->meta_key] = ( is_serialized( $m->meta_value ) ? invoize_mb_unserialize( $m->meta_value ) : $m->meta_value );
        }
        $email = $meta['invoice']['client']['email'];
        $dueDate = invoizeFormatDate( $meta['due_date'] );
        $invoiceDate = invoizeFormatDate( $meta['invoice_date'] );
        $invoiceNumber = $meta['id'];
        $clientName = $meta['invoice']['client']['name'];
        $token = $meta['token'];
        if ( !$email ) {
            throw new \Exception("Email doesn't exist", 400);
        }
        $templates = [];
        $templatesTab = Setting::tab( 'templates' )->get();
        foreach ( $templatesTab as $t ) {
            $templates[$t->option_name] = ( is_serialized( $t->option_value ) ? invoize_mb_unserialize( $t->option_value ) : $t->option_value );
        }
        $templateSetting = InvoizePlugin::getInstance()->getSlug() . '.templates';
        $template = NULL;
        switch ( $status ) {
            case static::UNPAID:
                $template = $templateSetting . '.unpaid_invoice';
                break;
            case static::PAID:
                $template = $templateSetting . '.paid_invoice';
                break;
            case static::EXPIRED:
                $template = $templateSetting . '.expired_invoice';
                break;
            case static::CANCELLED:
                $template = $templateSetting . '.canceled_invoice';
                break;
            case static::REMINDER_BEFORE:
                $template = $templateSetting . '.reminder_before';
                break;
            case static::REMINDER_AFTER:
                $template = $templateSetting . '.reminder_after';
                break;
            case static::RECURRING:
                $template = $templateSetting . '.recurring_invoice';
                break;
            default:
                throw new \Exception('Invalid status');
                break;
        }
        // if paid, only send receipt file
        $pdf = new PDFInvoice($invoice);
        $fileType = ( $meta['payment_status'] == static::PAID ? Invoice::RECEIPT : Invoice::INVOICE );
        $filePath = $pdf->generateFile( $fileType );
        $data = json_decode( $templates[$template] );
        $content = str_replace( "\n", "<br/>", $data->body );
        // add bold styling on template content inside {{ }}
        $content = preg_replace( '/\\{\\{(.*?)\\}\\}/', '<b>{{$1}}</b>', $content );
        // used inside the template
        $business = $meta['invoice']['business'];
        $businessName = $business['business_name'];
        $businessEmail = $business['email'];
        $businessPhone = $business['phone_number'];
        $businessAddress = $business['address'];
        $businessWeb = $business['web'];
        $businessWeb = ( $businessWeb && !preg_match( "/^https?:\\/\\//", $businessWeb ) ? "https://" . $businessWeb : $businessWeb );
        $businessLogo = $business['logo'];
        $invoiceUrl = esc_url( get_site_url() ) . '/invoize-preview?invoizeToken=' . $token;
        $showHeader = true;
        $isReceipt = $status == static::PAID;
        ob_start();
        include __DIR__ . '/../../templates/email/email-header.php';
        echo esc_html( $content );
        include __DIR__ . '/../../templates/email/invoice-link.php';
        include __DIR__ . '/../../templates/email/email-footer.php';
        $emailContent = ob_get_contents();
        ob_end_clean();
        $mail = new Mail();
        $mail->setRecipients( [$email] );
        $mail->setSubject( $data->subject, [
            'business_name'  => $businessName,
            'client_name'    => $clientName,
            'invoice_number' => $invoiceNumber,
            'invoice_date'   => $invoiceDate,
            'due_date'       => $dueDate,
        ] );
        if ( property_exists( $data, 'cc' ) ) {
            $mail->setCc( [$data->cc] );
        }
        if ( property_exists( $data, 'bcc' ) ) {
            $mail->setBcc( [$data->bcc] );
        }
        $mail->setCc( [$data->cc] );
        $mail->setBcc( [$data->bcc] );
        $mail->setContent( $emailContent, [
            'business_name'  => $businessName,
            'client_name'    => $clientName,
            'invoice_number' => $invoiceNumber,
            'invoice_date'   => $invoiceDate,
            'due_date'       => $dueDate,
        ] );
        $mail->setAttachment( $filePath );
        $mail->send();
        wp_delete_file( $filePath );
        // update is_email_sent meta
        $isSent = $invoice->metas()->where( 'meta_key', 'is_email_sent' )->firstOrNew();
        $isSent->meta_key = 'is_email_sent';
        $isSent->meta_value = true;
        $isSent->save();
        $history = $invoice->metas()->where( 'meta_key', 'action_history' )->first();
        $user = User::instance()->setContent( wp_get_current_user() )->getContent();
        if ( $isFromCron ) {
            $actionHistory = ActionHistory::instance()->setUser( $user )->setMessage( 'Email sent automatically from schedule' )->getContent();
        } else {
            $actionHistory = ActionHistory::instance()->setUser( $user )->setMessage( $user['name'] . ' has sent email this invoice' )->getContent();
        }
        if ( !$history ) {
            $meta = [
                'action_history' => [$actionHistory],
            ];
            $invoice->setMeta( $invoice->ID, $meta );
        } else {
            $arr = invoize_mb_unserialize( $history->meta_value );
            $arr[] = $actionHistory;
            $history->meta_value = serialize( $arr );
            $history->save();
        }
    }

    public static function createReceipt( $invoiceId ) {
        $prefix = Setting::key( 'receipt.prefix' )->first();
        $receiptNumber = Setting::getLastReceiptNumber();
        $receipt = Receipt::create( [
            'post_title'   => $prefix->option_value . $receiptNumber,
            'post_content' => $invoiceId,
            'post_excerpt' => Invoice::PAID,
        ] );
        $terms = Setting::key( 'receipt.termsAndConditions' )->first();
        Receipt::setMeta( $receipt->ID, [
            'terms'  => $terms->option_value,
            'prefix' => $prefix->option_value,
            'number' => $receiptNumber,
        ] );
        Setting::updateSetting( 'receipt.startFromNumber', Setting::getLastReceiptNumber() + 1 );
    }

    public function deleteReceipt() {
        $this->receipt()->delete();
    }

    public static function createFromWc( $orderId ) {
        $order = wc_get_order( $orderId );
        if ( !$order ) {
            throw new \Exception('Order not found', 404);
        }
        $prefix = Setting::key( 'invoice.prefix' )->value( 'option_value' );
        $number = Setting::getLastInvoiceNumber();
        $id = ( $prefix ? $prefix . $number : "#{$number}" );
        $customerId = $order->get_customer_id() ?? 0;
        // TODO: Make sure this not break anything
        $token = invoizeGenerateToken( $id, $customerId );
        $businessId = Setting::key( 'business.default' )->value( 'option_value' );
        if ( !$businessId ) {
            throw new \Exception('No default business entity saved', 404);
        }
        $business = Business::with( 'metas' )->find( $businessId );
        if ( !$business ) {
            throw new \Exception('No business entity found', 404);
        }
        $businessMetas = [];
        foreach ( $business->metas as $m ) {
            $businessMetas[$m->meta_key] = $m->meta_value;
        }
        $businessContent = [
            'id'            => $business->ID,
            'business_name' => $business->post_title,
            'phone_number'  => $businessMetas['phone_number'],
            'email'         => $businessMetas['email'],
            'web'           => $businessMetas['web'],
            'address'       => $businessMetas['address'],
            'zip'           => $businessMetas['zip'],
            'logo'          => wp_get_attachment_url( (int) $businessMetas['logo'] ),
        ];
        $clientContent = [
            'id'          => $order->get_user_id(),
            'name'        => $order->get_billing_first_name() . " " . $order->get_billing_last_name(),
            'email'       => $order->get_billing_email(),
            'phoneNumber' => $order->get_billing_phone(),
            'address'     => $order->get_billing_address_1() . " " . $order->get_billing_address_2(),
            'website'     => null,
            'zip'         => $order->get_billing_postcode(),
        ];
        $business = InvoiceBusiness::instance()->setContent( $businessContent )->getContent();
        $client = Client::instance()->setContent( $clientContent )->getContent();
        $currency = Currency::instance()->setName( $order->get_currency() )->setSymbol( get_woocommerce_currency_symbol( $order->get_currency() ) )->getContent();
        $total = $order->get_total();
        $orderDate = Carbon::instance( $order->get_date_created() );
        $dueDateInterval = Setting::key( 'invoice.dueDateInterval' )->first();
        if ( !$dueDateInterval ) {
            throw new \Exception('No due date interval exist in settings', 404);
        }
        $dueDateInterval = $dueDateInterval->option_value;
        $dueDateInterval = (int) explode( ' ', $dueDateInterval )[0];
        $dueDate = Carbon::now()->addDays( $dueDateInterval );
        $note = Setting::key( 'invoice.note' )->value( 'option_value' );
        $note = ( $note ?: null );
        $terms = Setting::key( 'invoice.termsAndConditions' )->value( 'option_value' );
        $terms = ( $terms ?: null );
        $status = $order->get_status();
        $integrationSetting = Setting::key( 'integration.woocommerce' )->value( 'option_value' );
        $integrationSetting = invoize_mb_unserialize( $integrationSetting );
        $paidOnCompleted = $status == 'completed' && $integrationSetting['setToPaidOn'] == 'completed';
        $paidOnProcessing = $status == 'processing' && $integrationSetting['setToPaidOn'] == 'processing';
        $status = ( $paidOnCompleted || $paidOnProcessing ? Invoice::PAID : Invoice::UNPAID );
        if ( !wp_get_current_user() ) {
            $actionHistory = ActionHistory::instance()->setUser( null )->setTo( $status )->setMessage( 'Order created this invoice' )->getContent();
        } else {
            $user = User::instance()->setContent( wp_get_current_user() )->getContent();
            $actionHistory = ActionHistory::instance()->setUser( $user )->setTo( Invoice::UNPAID )->setMessage( $user['name'] . ' has created this invoice' )->getContent();
        }
        $wcProducts = $order->get_items();
        $products = Products::instance();
        foreach ( $wcProducts as $item ) {
            if ( $item instanceof WC_Order_Item_Product ) {
                $product = $item->get_product();
                $products->addProduct( [
                    'id'        => $item->get_id(),
                    'name'      => $item->get_name(),
                    'unitPrice' => (float) $product->get_price(),
                    'quantity'  => $item->get_quantity(),
                    'amount'    => (float) $product->get_price() * $item->get_quantity(),
                ] );
            }
        }
        $products = $products->getContent();
        // Taxes
        $taxes = Taxes::instance();
        foreach ( $order->get_taxes() as $tax ) {
            $data = Tax::instance()->setContent( [
                'name'        => $tax->get_name(),
                'type'        => 'fixed',
                'value'       => (float) $tax->get_tax_total(),
                'description' => null,
            ] )->getContent();
            $taxes->addTax( $data );
        }
        if ( (int) $order->get_total_fees() > 0 ) {
            $data = Tax::instance()->setContent( [
                'name'        => 'Fees',
                'type'        => 'fixed',
                'value'       => (float) $order->get_total_fees(),
                'description' => null,
            ] )->getContent();
            $taxes->addTax( $data );
        }
        if ( (int) $order->get_shipping_total() > 0 ) {
            $data = Tax::instance()->setContent( [
                'name'        => 'Shipping total',
                'type'        => 'fixed',
                'value'       => (float) $order->get_shipping_total(),
                'description' => null,
            ] )->getContent();
            $taxes->addTax( $data );
        }
        $taxTotal = (float) $order->get_total_tax() + (float) $order->get_shipping_total() + (float) $order->get_total_fees();
        $taxes = $taxes->setTotal( $taxTotal )->getContent();
        // Discounts
        $discounts = Discounts::instance();
        if ( $order->get_total_discount() > 0 ) {
            $discount = Discount::instance()->setContent( [
                'name'        => 'Discount',
                'type'        => 'fixed',
                'value'       => $order->get_total_discount(),
                'description' => null,
            ] )->getContent();
            $discounts->addDiscount( $discount );
        }
        $discounts = $discounts->setTotal( $order->get_total_discount() )->getContent();
        $payments = [];
        $paymentMethod = $order->get_payment_method();
        $paymentMethodTitle = $order->get_payment_method_title();
        // empty payment method mean the order is created manually from admin, therefore use Invoize default payment setting.
        // if $isAutoCreate true mean it's created automatic when order woocommerce also created, then use the order payment method
        if ( empty( $paymentMethod ) ) {
            $defaultPayment = Setting::key( 'payment.default' )->value( 'option_value' );
            if ( !$defaultPayment ) {
                throw new \Exception('No default payment saved', 404);
            }
            $defaultPayment = invoize_mb_unserialize( $defaultPayment );
            // Bank payment
            if ( $defaultPayment['value'] == Payment::BANK ) {
                $banks = Setting::key( 'payment.banks' )->value( 'option_value' );
                if ( !$banks ) {
                    throw new \Exception('No bank account exist in settings', 404);
                }
                $banks = invoize_mb_unserialize( $banks );
                $defaultBank = Setting::key( 'payment.defaultBank' )->value( 'option_value' );
                if ( !$defaultBank ) {
                    throw new \Exception('No default bank account exist in settings', 404);
                }
                $bank = array_values( array_filter( $banks, function ( $bank ) use($defaultBank) {
                    return $bank['id'] == $defaultBank;
                } ) );
                if ( empty( $bank ) ) {
                    throw new \Exception('Bank account not found', 404);
                }
                $bank = $bank[0];
                $payments[] = [
                    'id'       => $bank['id'],
                    'currency' => $bank['currency']['name'],
                    'detail'   => $bank['detail'],
                    'method'   => Payment::BANK,
                    'name'     => $bank['name'],
                    'type'     => $bank['type'],
                ];
                // Paypal Direct Payment
            } else {
                if ( $defaultPayment['value'] == Payment::PAYPAL_DIRECT ) {
                    $paypal = Setting::key( 'payment.directPaypals' )->value( 'option_value' );
                    if ( !$paypal ) {
                        throw new \Exception('No paypal payment exist in settings', 404);
                    }
                    $paypal = ( is_serialized( $paypal ) ? invoize_mb_unserialize( $paypal )[0] : $paypal );
                    $payments[] = [
                        'method' => Payment::PAYPAL,
                        'type'   => 'direct payment',
                        'name'   => $paypal,
                    ];
                    // Paypal Auto Confirmation Payment
                } else {
                    if ( $defaultPayment['value'] == Payment::PAYPAL_AUTO_CONFIRMATION ) {
                        // Xendit payment
                    } else {
                        if ( $defaultPayment['value'] == Payment::XENDIT ) {
                        } else {
                            throw new \Exception('No payment method found', 404);
                        }
                    }
                }
            }
            // use payment method chosen by woocommerce customer
        } else {
            // if bank is chosen, use bank list from woocommerce
            if ( $paymentMethod == Payment::WOOCOMMERCE_BANK_CODE ) {
                $wcBanks = get_option( 'woocommerce_bacs_accounts' );
                if ( is_array( $wcBanks ) && count( $wcBanks ) > 0 ) {
                    $wcBankPayment = [
                        'method' => Payment::WOOCOMMERCE_TRANSACTION,
                        'type'   => Payment::WOOCOMMERCE_BANK,
                        'name'   => $paymentMethodTitle,
                        'detail' => [],
                    ];
                    foreach ( $wcBanks as $bank ) {
                        $detail = [];
                        foreach ( $bank as $key => $value ) {
                            if ( !empty( $value ) ) {
                                $detail[$key] = $value;
                            }
                        }
                        $wcBankPayment['detail'][] = $detail;
                    }
                    $payments[] = $wcBankPayment;
                }
                // if other payment, show link to order history
            } else {
                $orderListUrl = wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) );
                $payments[] = [
                    'method'   => Payment::WOOCOMMERCE . ' transaction',
                    'name'     => $paymentMethodTitle,
                    'detail'   => $orderListUrl,
                    'checkout' => $orderListUrl,
                ];
            }
        }
        $invoiceId = static::generateInvoice( [
            'id'            => $id,
            'prefix'        => $prefix,
            'number'        => $number,
            'token'         => $token,
            'business'      => $business,
            'client'        => $client,
            'paymentStatus' => $status,
            'invoiceStatus' => static::ACTIVE,
            'tab'           => $status,
            'orderDate'     => $orderDate->format( 'Y-m-d' ),
            'invoiceDate'   => Carbon::now()->format( 'Y-m-d' ),
            'dueDate'       => $dueDate->format( 'Y-m-d' ),
            'products'      => $products,
            'payments'      => $payments,
            'currency'      => $currency,
            'subtotal'      => (float) $order->get_subtotal(),
            'total'         => (float) $order->get_total(),
            'discounts'     => $discounts,
            'taxes'         => $taxes,
            'note'          => $note,
            'terms'         => $terms,
            'user'          => $user,
            'actionHistory' => $actionHistory,
            'wc_order_id'   => $orderId,
        ] );
        if ( $status == static::PAID ) {
            static::createReceipt( $invoiceId );
        }
        if ( $integrationSetting['sendOnPaid'] == 'true' || $integrationSetting['sendOnNewOrder'] == 'true' ) {
            static::sendMail( $invoiceId, $status );
        }
        return [
            'id'        => $id,
            'invoiceId' => $invoiceId,
            'token'     => $token,
        ];
    }

    public function editInvoice( $params ) {
        $id = $this->metas()->where( 'meta_key', 'id' )->value( 'meta_value' );
        $token = $this->metas()->where( 'meta_key', 'token' )->value( 'meta_value' );
        $orderDate = sanitize_text_field( $params['orderDate'] );
        $invoiceDate = sanitize_text_field( $params['invoiceDate'] );
        $dueDate = sanitize_text_field( $params['dueDate'] );
        $note = sanitize_textarea_field( $params['note'] );
        $terms = sanitize_textarea_field( $params['terms'] );
        $internalNote = sanitize_textarea_field( $params['internalNote'] );
        $subtotal = ( (float) $params['subtotal'] ?: 0 );
        $total = ( (float) $params['total'] ?: 0 );
        $products = Products::instance()->setContent( $params['products'] )->getContent();
        $business = InvoiceBusiness::instance()->setContent( $params['business'] )->getContent();
        $client = Client::instance()->setContent( $params['client'] )->getContent();
        $currency = Currency::instance()->setContent( $params['currency'] )->getContent();
        $discount = Discounts::instance()->setContent( $params['discount'] )->getContent();
        $tax = Taxes::instance()->setContent( $params['tax'] )->getContent();
        $reminders = Reminders::instance()->setContent( $params['reminder'], $dueDate )->getContent();
        $user = User::instance()->setContent( wp_get_current_user() )->getContent();
        // check for payment before edit.
        // if the payment is equal with payment after edit, use that value instead of
        // creating new payment.
        $lastPayments = $this->metas()->where( 'meta_key', 'payments' )->value( 'meta_value' );
        $lastPayments = invoize_mb_unserialize( $lastPayments );
        $lastPaymentsMethod = [];
        $lastPaymentsType = [];
        foreach ( $lastPayments as $p ) {
            $lastPaymentsMethod[] = $p['method'];
            $lastPaymentsType[] = $p['type'];
        }
        $lastTotal = $this->metas()->where( 'meta_key', 'total' )->value( 'meta_value' );
        $lastCurrency = $this->metas()->where( 'meta_key', 'currency' )->value( 'meta_value' );
        $lastCurrency = invoize_mb_unserialize( $lastCurrency );
        $payments = [];
        $getPayments = $params['payments'];
        foreach ( $getPayments as $p ) {
            $payment = [];
            foreach ( $p as $key => $value ) {
                if ( $key == 'detail' && is_array( $value ) ) {
                    $payment['detail'] = $value;
                    continue;
                }
                $payment[$key] = ( $key == 'detail' ? sanitize_textarea_field( $value ) : sanitize_text_field( $value ) );
            }
            // if last payment is paypal or xendit / third party payment,
            // use that checkout data.
            // add checkout data from last payment if last payment is paypal or xendit
            foreach ( $lastPayments as $lastPayment ) {
                if ( $lastPayment['method'] == Payment::XENDIT && $payment['method'] == Payment::XENDIT ) {
                    $payment['checkout'] = $lastPayment['checkout'];
                }
                if ( $lastPayment['method'] == Payment::PAYPAL && $lastPayment['type'] == Payment::AUTO_CONFIRMATION && $payment['method'] == Payment::PAYPAL && $payment['type'] == Payment::AUTO_CONFIRMATION ) {
                    $payment['checkout'] = $lastPayment['checkout'];
                }
                if ( $payment['method'] == Payment::PAYPAL && $payment['type'] == Payment::DIRECT_PAYMENT ) {
                    $payment['checkout'] = $payment['name'];
                }
            }
            $isLastPaymentPaypalAuto = in_array( Payment::PAYPAL, $lastPaymentsMethod ) && in_array( Payment::AUTO_CONFIRMATION, $lastPaymentsType );
            $isLastPaymentXendit = in_array( Payment::XENDIT, $lastPaymentsMethod );
            $isThisPaymentPaypal = $payment['method'] == Payment::PAYPAL;
            $isThisPaypalAutoConfirm = $payment['type'] == Payment::AUTO_CONFIRMATION;
            $isThisPaymentXendit = $payment['method'] == Payment::XENDIT;
            $isNewTotal = $lastTotal != $total || $lastCurrency['name'] != $currency['name'];
            $payments[] = $payment;
        }
        // Update action history
        $actionHistory = ActionHistory::instance()->setUser( $user )->setTo( Invoice::UNPAID )->setMessage( $user['name'] . ' has edited this invoice' )->getContent();
        $lastActionHistory = $this->metas()->where( 'meta_key', 'action_history' )->value( 'meta_value' );
        $lastActionHistory = invoize_mb_unserialize( $lastActionHistory );
        $lastActionHistory[] = $actionHistory;
        // update on wp_posts
        $this->post_content = $client['name'];
        $this->save();
        // update on wp_postmeta
        $invoiceMeta = [
            'invoice'         => [
                'business'             => $business,
                'client'               => $client,
                'billedTo'             => $params['billedTo'],
                'billedToSameAsClient' => $params['billedToSameAsClient'],
                'status'               => Invoice::UNPAID,
                'orderDate'            => $orderDate,
                'invoiceDate'          => $invoiceDate,
                'dueDate'              => $dueDate,
                'products'             => $products,
                'payments'             => $payments,
                'currency'             => $currency,
                'subtotal'             => $subtotal,
                'total'                => $total,
                'discount'             => $discount,
                'tax'                  => $tax,
                'reminders'            => [
                    'before' => $reminders['before'] ?? [],
                    'after'  => $reminders['after'] ?? [],
                ],
            ],
            'due_date'        => $dueDate,
            'invoice_date'    => $invoiceDate,
            'total'           => $total,
            'client_id'       => $client['id'],
            'business_id'     => $business['id'],
            'payments'        => $payments,
            'currency'        => $currency,
            'reminder_before' => $reminders['before'] ?? [],
            'reminder_after'  => $reminders['after'] ?? [],
            'action_history'  => $lastActionHistory,
            'invoice_note'    => [
                'note'         => $note ?? NULL,
                'terms'        => $terms ?? NULL,
                'internalNote' => $internalNote ?? NULL,
            ],
        ];
        $this->updateMeta( $this->ID, $invoiceMeta );
        $summaryTime = Carbon::parse( $invoiceDate );
        Setting::recalculateSummary( $summaryTime->month, $summaryTime->year );
        // set reminder
        Reminder::schedule_reminder();
        return $token;
    }

    // this will get query from invoices itself, not from the database/Settings model
    // private static function getSummaryFromInvoices(string $paymentStatus): array
    // {
    //     $invoices = Invoice::with('metas')->whereHas('metas', function ($metas) use ($paymentStatus) {
    //         $startOfMonth = Carbon::now()->startOfMonth();
    //         $endOfMonth = Carbon::now()->endOfMonth();
    //         $metas
    //             ->where('payment_status', $paymentStatus)
    //             ->where('meta_key', 'invoice_date')
    //             ->whereBetween('meta_value', [$startOfMonth, $endOfMonth]);
    //     })->get();
    //     $total = $invoices->map(function ($invoice) {
    //         $total = $invoice->metas->where('meta_key', 'total')->first();
    //         return (float) $total->meta_value;
    //     })->sum();
    //     $count = $invoices->count();
    //     return [$total, $count];
    // }
}
