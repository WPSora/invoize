<?php

namespace Invoize\Features\Integrations\Woocommerce;

use Invoize\Interfaces\HasPlugin;
use Invoize\Models\Invoice;
use Invoize\Models\Setting;
use Invoize\Models\States\Invoice\PaymentState\InvoiceUnpaidState;
use Invoize\Traits\InteractsWithPlugin;

class Woocommerce implements HasPlugin
{
    use InteractsWithPlugin;

    protected $settings = [];

    public function displayCustomColumnContent($column, $order)
    {

        if ($column != 'create_invoize_column') {
            return;
        }

        $orderId    = is_int($order) ? $order : $order->get_id();
        $order      = wc_get_order($orderId);

        $billinInformationExists = $order->get_billing_first_name() || $order->get_billing_last_name() || $order->get_billing_email();

        if (!$order->get_customer_id() && !$billinInformationExists) {
            echo "<p style='font-weight: 400; padding: 20px;color:#ddd;text-align:left;padding:0;'>
            Can't create invoice for guest order
            </p>";
            return;
        }

        wp_enqueue_script(
            'invoize-custom-column-wc-order',
            $this->plugin->getPluginAssetUrl('ivz-column-wc-order-list.js'),
            [],
            $this->plugin->getVersion(),
            true
        );

        wp_localize_script(
            "invoize-custom-column-wc-order",
            "invoize",
            ['wp_rest_nonce' => wp_create_nonce('wp_rest')]
        );

        $base_url   = admin_url("admin.php?page=invoize#/invoice/");

        $api_url    = rest_url($this->plugin->getSlug()) . '/api';


        $invoice = Invoice::whereHas('metas', function ($metas) use ($orderId) {
            $metas->where('meta_key', 'wc_order_id')->where('meta_value', $orderId);
        })->first();

        if (!empty($invoice)) :
            $href   = $base_url . $invoice->getToken();
            $id     = $invoice->post_title; ?>

            <a href="<?php echo esc_url($href); ?>" class="button button-primary" target="_blank">
                <?php echo esc_html($id); ?>
            </a>
        <?php
            return;
        endif; ?>

        <a style="font-weight: 600; padding: 20px 0px;" class="create-invoize-button-<?php echo esc_attr($orderId) ?>" href='#' onclick="createInvoiceNow(this)" data-order-id="<?php echo esc_attr($orderId) ?>" data-api="<?php echo esc_url($api_url) ?>" data-base-url="<?php echo esc_url($base_url) ?>">
            + Create Invoice
        </a>
<?php
    }

    public function customColumns($columns)
    {
        $reordered_columns = array();

        // Inserting columns to a specific location
        foreach ($columns as $key => $column) {
            $reordered_columns[$key] = $column;

            if ($key ===  'order_status') {
                // Inserting after "Status" column
                $reordered_columns['create_invoize_column'] = 'Invoize';
            }
        }
        return $reordered_columns;
    }

    private function loadSettings()
    {
        $integrationSetting         = Setting::key('integration.woocommerce')->value('option_value');
        $this->settings             = unserialize($integrationSetting);
        $this->settings['hpos']     = get_option('woocommerce_custom_orders_table_enabled') == 'yes';
    }

    public function run()
    {
        $this->loadSettings();
        $hookRegistry = $this->plugin->getHookRegistry();

        // Filters
        $hookRegistry->add_filter(
            $this->settings['hpos']
                ? 'manage_woocommerce_page_wc-orders_columns'
                : 'manage_edit-shop_order_columns',
            [$this, 'customColumns'],
            100,
            1
        );

        // Actions
        $hookRegistry->add_action(
            $this->settings['hpos']
                ? 'manage_woocommerce_page_wc-orders_custom_column'
                : 'manage_shop_order_posts_custom_column',
            [$this, 'displayCustomColumnContent'],
            100,
            2
        );

        //Actions Orders
        $hookRegistry->add_action(
            'woocommerce_new_order',
            function ($orderId) {
                if ($this->settings['createOnNewOrder'] == 'true') {
                    $order      = wc_get_order($orderId);
                    $createdVia = $order->get_created_via();

                    // Only run this function if order created from admin, not store
                    $checkValue = ['store-api', 'rest-api', 'checkout'];

                    if (in_array($createdVia, $checkValue)) {
                        return;
                    }

                    $invoice = Invoice::with('metas')->whereHas('metas', function ($metas) use ($orderId) {
                        $metas->where('meta_key', 'wc_order_id')->where('meta_value', $orderId);
                    })->first();

                    if (!$invoice) {
                        try {
                            Invoice::createFromWc($orderId);
                        } catch (\Exception $e) {
                            throw new \Exception(
                                esc_html('Failed to create Invoize. Error message: ' . $e->getMessage()),
                                esc_html($e->getCode())
                            );
                        }
                    }
                }
            },
        );

        $hookRegistry->add_action(
            'woocommerce_thankyou',
            function ($orderId) {
                if ($this->settings['createOnNewOrder'] == 'true') {
                    $invoice = Invoice::findbyOrderId($orderId);

                    if (!$invoice) {
                        try {
                            Invoice::createFromWc($orderId);
                        } catch (\Exception $e) {
                            throw new \Exception(
                                esc_html('Failed to create Invoize. Error message: ' . $e->getMessage()),
                                esc_html($e->getCode())
                            );
                        }
                    }
                }
            },
        );

        $hookRegistry->add_action(
            'woocommerce_order_status_processing',
            function ($orderId) {
                if ($this->settings['setToPaidOn'] == 'processing') {
                    $invoice = Invoice::findbyOrderId($orderId);

                    if (!$invoice) {
                        try {
                            Invoice::createFromWc($orderId);
                            $invoice = Invoice::findbyOrderId($orderId);
                        } catch (\Exception $e) {
                            throw new \Exception(
                                esc_html('Failed to create Invoize. Error message: ' . $e->getMessage()),
                                esc_html($e->getCode())
                            );
                        }
                    }

                    $invoiceState = $invoice->getPaymentState();

                    if ($invoiceState instanceof InvoiceUnpaidState) {
                        $sendReceipt = $this->settings['sendOnPaid'] == 'true';
                        $invoiceState->pay($sendReceipt);
                    }
                }
            },
        );

        $hookRegistry->add_action(
            'woocommerce_order_status_completed',
            function ($orderId) {
                if ($this->settings['setToPaidOn'] == 'completed') {
                    $invoice = Invoice::findbyOrderId($orderId);

                    if (!$invoice) {
                        try {
                            Invoice::createFromWc($orderId);
                            $invoice = Invoice::findbyOrderId($orderId);
                        } catch (\Exception $e) {
                            throw new \Exception(
                                esc_html('Failed to create Invoize. Error message: ' . $e->getMessage()),
                                esc_html($e->getCode())
                            );
                        }
                    }

                    $invoiceState = $invoice->getPaymentState();

                    if ($invoiceState instanceof InvoiceUnpaidState) {
                        $sendReceipt = $this->settings['sendOnPaid'] == 'true';
                        $invoiceState->pay($sendReceipt);
                    }
                }
            },
        );

        $hookRegistry->add_action(
            'woocommerce_order_status_cancelled',
            function ($orderId) {
                if (isset($this->settings['cancelOnCancelOrder']) && $this->settings['cancelOnCancelOrder'] == 'true') {
                    $invoice = Invoice::findbyOrderId($orderId);

                    $invoiceState = $invoice->getInvoiceState();
                    $invoiceState->cancel();
                }
            },
        );

        $hookRegistry->add_action(
            'woocommerce_delete_order',
            function ($orderId) {
                $invoice = Invoice::findbyOrderId($orderId);

                if ($invoice) {
                    wp_delete_post($invoice->ID);
                }
            }
        );

        $hookRegistry->add_action(
            'woocommerce_untrash_order',
            function ($orderId) {
                $invoice = Invoice::findbyOrderId($orderId);

                if ($invoice) {
                    $invoiceState = $invoice->getInvoiceState();
                    $invoiceState->activate();
                }
            }
        );

        $hookRegistry->add_action(
            'woocommerce_trash_order',
            function ($orderId) {
                $invoice = Invoice::findbyOrderId($orderId);

                if ($invoice) {
                    $invoiceState = $invoice->getInvoiceState();
                    $invoiceState->trash();
                }
            }
        );
    }
}
