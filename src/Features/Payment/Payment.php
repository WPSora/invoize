<?php

namespace Invoize\Features\Payment;

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

            $paymentOnWoocommerce = invoizeGetOption('payment.paymentOnWoocommerce');
            $paymentOnWoocommerce = is_serialized($paymentOnWoocommerce) ? invoize_mb_unserialize($paymentOnWoocommerce) : $paymentOnWoocommerce;
            $paymentOnWoocommerce = array_filter($paymentOnWoocommerce);

            if (strpos($invoice['payments'][0]['method'], 'woocommerce') !== false && $paymentOnWoocommerce) {
                $payments = [];
                foreach ($paymentOnWoocommerce as $paymentOnWc) {
                    if ($paymentOnWc == ModelsPayment::BANK) {
                        $banks          = invoizeGetOption('payment.banks', []);

                        if (!$banks) continue;

                        $defaultBank    = Setting::key('payment.defaultBank')->value('option_value');
                        $defaultBank    = array_filter($banks, function ($bank) use ($defaultBank) {
                            return $bank['id'] == $defaultBank;
                        });

                        $defaultBank = reset($defaultBank);

                        // Should set the default bank
                        if (!$defaultBank) continue;
                        $payments[] = [
                            'name' => $defaultBank['name'],
                            'method' => ModelsPayment::BANK,
                            'currency' => $defaultBank['currency']['name'],
                            'type' => $defaultBank['type'],
                            'detail' => $defaultBank['detail'],
                        ];
                    } else {
                        $payments[] = ['method' => $paymentOnWc, 'name' => $paymentOnWc];
                    }

                    $invoice['payments'] = $payments;
                }
            }


            add_action("wp_enqueue_scripts", function () use ($invoice) {
                $options = array_map(function ($record) {
                    if (
                        $record['method'] == ModelsPayment::PAYPAL &&
                        $record['type'] == ModelsPayment::DIRECT_PAYMENT
                    ) {
                        $record['name'] = "Paypal (Direct Transfer)";
                        $record['method'] = ModelsPayment::PAYPAL_DIRECT;
                    }

                    if (
                        $record['method'] == ModelsPayment::PAYPAL &&
                        $record['type'] == ModelsPayment::AUTO_CONFIRMATION
                    ) {
                        $record['name'] = 'Paypal (Auto Confirmation)';
                        $record['method'] = ModelsPayment::PAYPAL_AUTO_CONFIRMATION;
                    }

                    return [
                        'label' => $record['name'],
                        'value' => $record['method'],
                    ];
                }, $invoice['payments']);

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

                $bankInfo = array_filter($invoice['payments'], fn($data) => $data['method'] == ModelsPayment::BANK);
                $bankInfo = reset($bankInfo);

                $directPaypalInfo = array_filter($invoice['payments'], function ($data) {
                    return ($data['method'] === ModelsPayment::PAYPAL
                        && $data['type'] === ModelsPayment::DIRECT_PAYMENT)
                        || $data['method'] === ModelsPayment::PAYPAL_DIRECT;
                });
                $directPaypalInfo = reset($directPaypalInfo);

                // if from woocommerce, add payment link from setting
                if ($directPaypalInfo['method'] == ModelsPayment::PAYPAL_DIRECT) {
                    $paymentLink = invoizeGetOption('payment.directPaypals');
                    if (!$paymentLink) {
                        $directPaypalInfo = null;
                        $columns[1]['value'] = array_values(array_filter($columns[1]['value'], function ($data) {
                            return $data['value'] !== 'paypal-direct';
                        }));
                    } else {
                        $directPaypalInfo['name'] = reset($paymentLink);
                    }
                }

                $woocommerceInfo = array_filter($invoice['payments'], fn($data) => $data['method'] == ModelsPayment::WOOCOMMERCE_TRANSACTION);
                $woocommerceInfo = reset($woocommerceInfo);

                wp_localize_script(
                    // enable for build
                    "invoize-admin-js",
                    // enable for dev
                    // "invoize-vite-main-js",
                    "invoize_payment",
                    [
                        'token' => $invoice['token'],
                        'default_payment' => $options[0]['value'],
                        'business' => [
                            'name' => $invoice['business']['business_name'] ?? '',
                            'logo' => $invoice['business']['logo'] ?? '',
                        ],
                        'payment_status' => $invoice['paymentStatus'],
                        'invoice_status' => $invoice['invoiceStatus'],
                        'bank_information' => isset($bankInfo['detail']) ? $bankInfo['detail'] : null,
                        'paypal_information' => $directPaypalInfo['name'],
                        'woocommerce_information' => $woocommerceInfo,
                        'columns'  => apply_filters(
                            'invoize_payment_columns',
                            $columns,
                            $invoice
                        ),
                        'highlight_columns'  => apply_filters(
                            'invoize_payment_highlight_columns',
                            $highlighColumns,
                            $invoice
                        ),
                        'subheading_text' => apply_filters(
                            'invoize_payment_subheading_text',
                            'Select your payment method',
                            $invoice
                        ),
                        'footer_text' => apply_filters(
                            'invoize_payment_footer_text',
                            'By clicking “Proceed to Payment,” you will be redirected to the payment page.',
                            $invoice
                        ),
                    ]
                );
            });

            Routes::load(
                $this->plugin->getPluginDir() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'payment' . DIRECTORY_SEPARATOR . 'payment.php'
            );
        });
    }
}
