<?php

namespace Invoize\Features\Front;

use Invoize\Interfaces\HasPlugin;
use Invoize\Models\Invoice;
use Invoize\Traits\InteractsWithPlugin;

class Front implements HasPlugin
{
    use InteractsWithPlugin;

    public function run()
    {
        $this->plugin->hookRegistry->add_filter('woocommerce_account_orders_columns', [$this, 'accountOrderInvoiceColumn']);
        $this->plugin->hookRegistry->add_action("woocommerce_my_account_my_orders_column_invoice", [$this, 'addMyAccountInvoiceColumnContent']);
    }

    public function accountOrderInvoiceColumn($columns)
    {
        $columns = array_slice($columns, 0, array_search('order-actions', array_keys($columns)), true) +
            ['invoice' => 'Invoice'] +
            array_slice($columns, array_search('order-actions', array_keys($columns)), null, true);
        return $columns;
    }

    public function addMyAccountInvoiceColumnContent($order)
    {
        $invoize = Invoice::findbyOrderId($order->get_id());
        if (!$invoize) {
            echo 'No invoice found';
        } else {
            $url = home_url('/invoize-preview/' . $invoize->getToken());
            echo '<a href="' . esc_url($url) . '" target="_blank">' . esc_html($invoize->getInvoiceNumber()) . '</a>';
        }
    }
}
