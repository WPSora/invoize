<?php

namespace Invoize\Classes;

use Invoize\InvoizePlugin;
use Invoize\Models\Invoice;

class Widget
{
    public static function getInstance()
    {
        return new self;
    }

    public function loadWidget()
    {
        if (!invoizeCheckPermissionIsAllowed()) {
            return;
        }

        $plugin = InvoizePlugin::getInstance();

        wp_enqueue_script('invoize-widget', $plugin->getPluginAssetUrl('widget.js'), [], $plugin->getVersion(), true);

        wp_localize_script(
            "invoize-widget",
            "invoize",
            ['api_url' => esc_url(rest_url($plugin->getSlug()) . '/api')]
        );

        wp_enqueue_style('invoize-widget', $plugin->getPluginAssetUrl('widget.css'));

        wp_add_dashboard_widget(
            $plugin->getSlug(),
            $plugin->getPluginName(),
            [$this, 'createWidget'],
        );
    }

    public function createWidget()
    {
        $plugin = InvoizePlugin::getInstance();
        $expiredSoonList = Invoice::getExpiredSoon(10, 7, true);
        $countExpiredSoon = Invoice::expiredSoon()->count();
        $unpaidList = Invoice::unpaidArchive()->orderBy('post_date', 'desc')->take(10)->get()
            ->map(function ($inv) {
                $number = $inv->metas()->firstWhere('meta_key', 'number');
                if ($number) {
                    $number = $number->meta_value;
                }
                $number = $number ? "#$number" : $inv->post_title;
                $total = $inv->metas()->firstWhere('meta_key', 'total');
                $token = $inv->metas()->firstWhere('meta_key', 'token');
                $currencyMeta = $inv->metas()->firstWhere('meta_key', 'currency');
                $currency = unserialize($currencyMeta->meta_value);
                return [
                    'id' => $inv->ID,
                    'name' => $number . ' ' . $inv->post_content,
                    'total' => $currency['name'] == 'IDR'
                        ? number_format($total->meta_value, 0, ',', '.')
                        : number_format($total->meta_value, 0, '.', ','),
                    'currency' => $currency['symbol'],
                    'token' => $token->meta_value,
                ];
            });

        $tabs = [
            Invoice::PAID,
            Invoice::UNPAID,
            Invoice::EXPIRED,
            Invoice::ARCHIVED,
            Invoice::CANCELLED,
            Invoice::TRASHED
        ];

        $count = Invoice::getInvoiceCount($tabs);
?>
        <div class="invoize-widget-wrapper">
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <!-- UNPAID -->
                <div class="invoize-long-tab-wrapper">
                    <a href="<?php echo esc_url(admin_url("admin.php?page=invoize#/invoices")) ?>" class="invoize-invoice-link long-tab-header">
                        Unpaid
                    </a>
                </div>
                <div class="invoize-invoice-wrapper">
                    <?php if (count($unpaidList) == 0) : ?>
                        <div style="font-style: italic; color: #6E7A8A">
                            You have no unpaid invoice
                        </div>
                    <?php else : ?>
                        <?php foreach ($unpaidList as $invoice) : ?>
                            <div class="invoize-invoice-link-wrapper">
                                <a class="invoize-invoice-link invoize-invoice-name" href='<?php echo esc_url(admin_url("admin.php?page=invoize#/invoice/" . $invoice['token'])) ?>'>
                                    <?php echo esc_html($invoice['name']) ?>
                                </a>
                                <div style="display: flex; gap: 8px; flex-wrap: nowrap;">
                                    <!-- total -->
                                    <div><?php echo esc_html($invoice['currency'] . $invoice['total']) ?></div>
                                    <!-- set to paid -->
                                    <div style="color: #dadada;">|</div>
                                    <a href="#" onclick="openConfirmModal(this)" data-invoice-id="<?php echo esc_attr($invoice['id']) ?>" data-invoice-name="<?php echo esc_attr($invoice['name']) ?>" class="invoize-set-to-paid invoize-unpaid-set-to-paid-<?php echo esc_attr($invoice['id']) ?>">
                                        Paid
                                    </a>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                </div>

                <!-- EXPIRED SOON -->
                <div class="invoize-long-tab-wrapper">
                    <a href="<?php echo esc_url(admin_url("admin.php?page=invoize#/invoices")) ?>" class="invoize-invoice-link long-tab-header" style="color: #EF4444;">
                        Expired Soon (in 7 days)
                    </a>
                </div>
                <div class="invoize-invoice-wrapper">
                    <?php if (count($expiredSoonList) == 0) : ?>
                        <div style="font-style: italic; color: #6E7A8A">
                            You have no invoice expiring soon
                        </div>
                    <?php else : ?>
                        <?php foreach ($expiredSoonList as $invoice) : ?>
                            <div class="invoize-invoice-link-wrapper">
                                <a class="invoize-invoice-link invoize-invoice-name" href='<?php echo esc_url(admin_url("admin.php?page=invoize#/invoice/" . $invoice['token'])) ?>'>
                                    <?php echo esc_html($invoice['name']) ?>
                                </a>
                                <div style="display: flex; gap: 8px; flex-wrap: nowrap;">
                                    <!-- total -->
                                    <div><?php echo esc_html($invoice['currency'] . $invoice['total']) ?></div>
                                    <div style="color: #dadada;">|</div>
                                    <!-- set to paid -->
                                    <a href="#" onclick="openConfirmModal(this)" data-invoice-id="<?php echo esc_attr($invoice['id']) ?>" data-invoice-name="<?php echo esc_attr($invoice['name']) ?>" class="invoize-set-to-paid invoize-expired-set-to-paid-<?php echo esc_attr($invoice['id']) ?>">
                                        Paid
                                    </a>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                </div>

                <div>
                    <!-- UNPAID -->
                    <div class="invoize-short-tab-wrapper">
                        <a href="<?php echo esc_url(admin_url("admin.php?page=invoize#/invoices")) ?>" class="invoize-invoice-link">
                            Unpaid
                        </a>
                        <div style="color: <?php echo esc_attr(count($unpaidList) ? '#EF4444' : '#f1f1f1') ?>;" class="invoize-tab-count">
                            <?php echo esc_html(count($unpaidList)) ?>
                        </div>
                    </div>

                    <!-- PAID -->
                    <div class="invoize-short-tab-wrapper">
                        <a href="<?php echo esc_url(admin_url("admin.php?page=invoize#/invoices")) ?>" class="invoize-invoice-link">
                            Paid
                        </a>
                        <div style="color: <?php echo esc_attr($count[Invoice::PAID] ? '#16a34a' : '#f1f1f1') ?>;" class="invoize-tab-count">
                            <?php echo esc_html($count[Invoice::PAID]) ?>
                        </div>
                    </div>

                    <!-- EXPIRED SOON -->
                    <div class="invoize-short-tab-wrapper">
                        <a href="<?php echo esc_url(admin_url("admin.php?page=invoize#/invoices")) ?>" class="invoize-invoice-link">
                            Expired Soon
                        </a>
                        <div style="color: <?php echo esc_attr($countExpiredSoon > 0 ? '#64748b' : '#f1f1f1') ?>;" class="invoize-tab-count">
                            <?php echo esc_html($countExpiredSoon) ?>
                        </div>
                    </div>

                    <!-- EXPIRED -->
                    <div class="invoize-short-tab-wrapper">
                        <a href="<?php echo esc_url(admin_url("admin.php?page=invoize#/invoices")) ?>" class="invoize-invoice-link">
                            Expired
                        </a>
                        <div style="color: <?php echo esc_attr($count[Invoice::EXPIRED] > 0 ? '#64748b' : '#f1f1f1') ?>" class="invoize-tab-count">
                            <?php echo esc_html($count[Invoice::EXPIRED]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div class="invoize-confirm-paid-modal-wrapper">
            <div class="invoize-confirm-paid-modal">
                <div class="invoize-confirm-paid-modal-header">Are you sure?</div>
                <div class="invoize-confirm-paid-modal-message">Are you sure?</div>
                <div class="invoize-confirm-paid-modal-email">
                    <input type="checkbox" id="is-send-email" name="is-send-email" value="is-send-email" />
                    <label for="is-send-email">Send receipt to customer's email</label>
                </div>
                <div class="invoize-confirm-paid-modal-action-wrap">
                    <button class="invoize-confirm-paid-modal-button invoize-confirm-paid-modal-no" onclick="closeConfirmModal()">
                        Cancel
                    </button>
                    <button class="invoize-confirm-paid-modal-button invoize-confirm-paid-modal-yes" onclick="updateInvoiceToPaid()">
                        Set to Paid
                    </button>
                </div>
            </div>
            <div class="invoize-loading-modal">
                <div>
                    <div style="width: 100%; display: flex; justify-content: center;">
                        <img alt="loading" src="<?php echo esc_url($plugin->getPluginUrl('public/loading-dot.gif')) ?>" width="70" class="invoize-loading-icon" />
                    </div>
                    <div class="invoize-loading-modal-message">Processing...</div>
                </div>
                <button class="invoize-confirm-paid-modal-button invoize-confirm-paid-modal-yes invoize-confirm-finish-button" onclick="handleCloseLoadingModal()">
                    Finish
                </button>
            </div>
        </div>
<?php
    }
}
