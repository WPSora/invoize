<?php

namespace Invoize\Features\Payment;

use Carbon\Carbon;
use Invoize\Interfaces\HasPlugin;
use Invoize\Models\Invoice;
use Invoize\Models\Payment as ModelsPayment;
use Invoize\Models\Setting;
use Invoize\Traits\InteractsWithPlugin;
use Routes;

class Payment implements HasPlugin
{
    use InteractsWithPlugin;

    public function run()
    {
        Routes::map('invoize-payment:token', function ($params) {
            wp_redirect(home_url("invoize/payment/{$params['token']}"));
            exit;
        });

        Routes::map('invoize/payment/:token', function ($params) {
            $invoice = Invoice::findByToken($params['token']);
            $invoice = $invoice->detail();
            $allPayments = ModelsPayment::getAllAvailablePayments();

            $paymentOnWoocommerce = invoizeGetOption('payment.paymentOnWoocommerce');
            $paymentOnWoocommerce = is_serialized($paymentOnWoocommerce)
                ? invoize_mb_unserialize($paymentOnWoocommerce)
                : $paymentOnWoocommerce;
            $paymentOnWoocommerce = array_filter($paymentOnWoocommerce);

            // only add payment method based on checked value in settings if it is woocommerce order
            if ($invoice['isWoocommerce'] && $paymentOnWoocommerce) {
                $allPayments = array_values(array_filter($allPayments, function ($payment) use ($paymentOnWoocommerce) {
                    return in_array($payment['method'], $paymentOnWoocommerce);
                }));
            }

            add_action("wp_enqueue_scripts", function () use ($invoice, $allPayments) {
                $options = array_map(function ($record) {
                    return [
                        'label' => $record['name'],
                        'value' => $record['method'],
                    ];
                }, $allPayments);

                $columns = [
                    [
                        'name'  => 'invoice_number',
                        'label' => 'Invoice Number',
                        'type'  => 'text',
                        'value' => $invoice['invoiceNumber'],
                    ],
                    [
                        'name'          => 'payment_methods',
                        'label'         => 'Payment Methods',
                        'type'          => 'options',
                        'description'   => 'Choose your payment',
                        'value'         => $options
                    ],
                ];

                $highlighColumns = [
                    [
                        'name'  => 'total_amount',
                        'label'  => 'Total Amount',
                        'value' => invoizeFormatCurrency($invoice['currency']['name'], $invoice['total'])
                    ]
                ];

                $bankInfo = reset(array_filter($allPayments, fn($data) => $data['method'] == ModelsPayment::BANK));

                $directPaypalInfo = reset(array_filter(
                    $allPayments,
                    fn($data) => $data['method'] === ModelsPayment::PAYPAL_DIRECT
                ));

                wp_localize_script(
                    // enable for build
                    "invoize-admin-js",
                    // enable for dev
                    // "invoize-vite-main-js",
                    "invoize_payment",
                    [
                        'token'           => $invoice['token'],
                        'default_payment' => $options[0]['value'],
                        'business' => [
                            'name' => $invoice['business']['business_name'] ?? '',
                            'logo' => $invoice['business']['logo'] ?? '',
                        ],
                        'payment_status'          => $invoice['paymentStatus'],
                        'paid_information'        => [
                            'date'   => Carbon::parse($invoice['paidDate'])->format('d-M-Y'),
                            'method' => ucfirst($invoice['paidMethod'])
                        ],
                        'invoice_status'          => $invoice['invoiceStatus'],
                        'bank_information'        => isset($bankInfo['detail']) ? $bankInfo['detail'] : null,
                        'paypal_information'      => $directPaypalInfo['checkout'],
                        'woocommerce_information' => null,
                        'columns'                 => $columns,
                        'highlight_columns'       => $highlighColumns,
                        'subheading_text'         => 'Select your payment method',
                        'footer_text'             => 'By clicking “Proceed to Payment,” you will be redirected to the payment page.',
                    ]
                );
            });

            Routes::load(
                $this->plugin->getPluginDir() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'payment' . DIRECTORY_SEPARATOR . 'payment.php'
            );
        });
    }
}
