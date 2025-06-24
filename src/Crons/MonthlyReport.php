<?php

namespace Invoize\Crons;

use Invoize\Classes\Mail;
use Carbon\Carbon;
use Invoize\Crons\Cron;
use Invoize\Models\Business;
use Invoize\Models\Invoice;
use Invoize\Models\Recurring;
use Pelago\Emogrifier\CssInliner;
class MonthlyReport extends Cron {
    protected $name = 'invoize_monthly_report';

    protected $schedule = 'daily';

    private function canRun() {
        return Carbon::tomorrow()->day == 1;
        // run on the end of the month
    }

    public function handle() {
        if ( $this->canRun() ) {
            $invoices = Invoice::thisMonth()->get();
            $currencies = collect( $invoices )->map( fn( $invoice ) => $invoice->getCurrency() )->unique();
            $invoices = [];
            foreach ( $currencies as $currency ) {
                $totalPaid = Invoice::thisMonth()->paid()->whereHas( 'metas', function ( $query ) use($currency) {
                    return $query->where( 'meta_key', 'currency' )->where( 'meta_value', 'LIKE', '%"' . $currency['symbol'] . '"%' );
                } )->count();
                $totalUnpaid = Invoice::thisMonth()->unpaid()->whereHas( 'metas', function ( $query ) use($currency) {
                    return $query->where( 'meta_key', 'currency' )->where( 'meta_value', 'LIKE', '%"' . $currency['symbol'] . '"%' );
                } )->count();
                $totalPaidLastMonth = Invoice::lastMonth()->paid()->whereHas( 'metas', function ( $query ) use($currency) {
                    return $query->where( 'meta_key', 'currency' )->where( 'meta_value', 'LIKE', '%"' . $currency['symbol'] . '"%' );
                } )->count();
                $totalUnpaidLastMonth = Invoice::lastMonth()->unpaid()->whereHas( 'metas', function ( $query ) use($currency) {
                    return $query->where( 'meta_key', 'currency' )->where( 'meta_value', 'LIKE', '%"' . $currency['symbol'] . '"%' );
                } )->count();
                $totalNominalPaid = Invoice::thisMonth()->paid()->whereHas( 'metas', function ( $query ) use($currency) {
                    return $query->where( 'meta_key', 'currency' )->where( 'meta_value', 'LIKE', '%"' . $currency['symbol'] . '"%' );
                } )->get()->sum( fn( $invoice ) => $invoice->getTotal() );
                $totalNominalUnpaid = Invoice::thisMonth()->unpaid()->whereHas( 'metas', function ( $query ) use($currency) {
                    return $query->where( 'meta_key', 'currency' )->where( 'meta_value', 'LIKE', '%"' . $currency['symbol'] . '"%' );
                } )->get()->sum( fn( $invoice ) => $invoice->getTotal() );
                $totalNominalPaidLastMonth = Invoice::lastMonth()->paid()->whereHas( 'metas', function ( $query ) use($currency) {
                    return $query->where( 'meta_key', 'currency' )->where( 'meta_value', 'LIKE', '%"' . $currency['symbol'] . '"%' );
                } )->get()->sum( fn( $invoice ) => $invoice->getTotal() );
                $totalNominalUnpaidLastMonth = Invoice::lastMonth()->unpaid()->whereHas( 'metas', function ( $query ) use($currency) {
                    return $query->where( 'meta_key', 'currency' )->where( 'meta_value', 'LIKE', '%"' . $currency['symbol'] . '"%' );
                } )->get()->sum( fn( $invoice ) => $invoice->getTotal() );
                $totalTransaction = $totalPaid + $totalUnpaid;
                $totalTransactionLastMonth = $totalPaidLastMonth + $totalUnpaidLastMonth;
                $totalNominal = $totalNominalPaid + $totalNominalUnpaid;
                $totalNominalLastMonth = $totalNominalPaidLastMonth + $totalNominalUnpaidLastMonth;
                $invoices[$currency['name']] = [
                    'symbol'                      => $currency['symbol'],
                    'paid'                        => $totalPaid,
                    'unpaid'                      => $totalUnpaid,
                    'totalTransactions'           => $totalTransaction,
                    'paidLastMonth'               => $totalPaidLastMonth,
                    'unpaidLastMonth'             => $totalUnpaidLastMonth,
                    'totalTransactionsLastMonth'  => $totalTransactionLastMonth,
                    'transactionPercentage'       => ( !$totalTransactionLastMonth ? null : round( ($totalTransaction - $totalTransactionLastMonth) / $totalTransactionLastMonth * 100 ) ),
                    'totalNominalPaid'            => $totalNominalPaid,
                    'totalNominalUnpaid'          => $totalNominalUnpaid,
                    'totalNominal'                => $totalNominal,
                    'totalNominalPaidLastMonth'   => $totalNominalPaidLastMonth,
                    'totalNominalUnpaidLastMonth' => $totalNominalUnpaidLastMonth,
                    'totalNominalLastMonth'       => $totalNominalLastMonth,
                    'nominalPercentage'           => ( !$totalNominalLastMonth ? null : round( ($totalNominal - $totalNominalLastMonth) / $totalNominalLastMonth * 100 ) ),
                ];
            }
            $businessName = get_post_meta( Business::getDefault()->ID, 'business_name', true );
            $totalPaid = Invoice::thisMonth()->paid()->count();
            $totalPaidLastMonth = Invoice::lastMonth()->paid()->count();
            $totalUnpaid = Invoice::thisMonth()->unpaid()->count();
            $totalUnpaidLastMonth = Invoice::lastMonth()->unpaid()->count();
            $transactionsLastMonth = $totalPaidLastMonth + $totalUnpaidLastMonth;
            $transactionsThisMonth = $totalPaid + $totalUnpaid;
            $invoiceSummaryPercentage = ( !$transactionsLastMonth ? null : round( ($transactionsThisMonth - $transactionsLastMonth) / $transactionsLastMonth * 100 ) );
            $recurrings = [];
            $totalRecurringThisMonth = collect( $recurrings )->sum( fn( $recurring ) => $recurring['totalRecurring'] );
            $totalRecurringLastMonth = collect( $recurrings )->sum( fn( $recurring ) => $recurring['totalRecurringLastMonth'] );
            $totalRecurringPercentage = ( !$totalRecurringLastMonth ? null : round( ($totalRecurringThisMonth - $totalRecurringLastMonth) / $totalRecurringLastMonth * 100 ) );
            $upcomingExpired = Invoice::expiredSoon( 7 )->take( 5 )->get();
            $expiredThisMonth = Invoice::expired()->take( 5 )->get();
            ob_start();
            require_once __DIR__ . '/../../templates/email/monthly-report.php';
            $content = ob_get_contents();
            ob_end_clean();
            $content = CssInliner::fromHtml( $content )->inlineCss()->render();
            try {
                $mail = new Mail();
                $mail->setRecipients( [get_option( 'admin_email' )] );
                $mail->setSubject( "Invoize Monthly Report" );
                $mail->setContent( $content );
                $mail->send();
            } catch ( \Exception $e ) {
                error_log( $e->getMessage() );
            }
        }
    }

}
