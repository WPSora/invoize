<?php
defined('ABSPATH') or die('No script kiddies please!');

use chillerlan\QRCode\QRCode;
use Invoize\Models\Invoice;
use Invoize\Models\Payment;
use Invoize\Models\Quotation;

$isQuotation    = get_class($record) == Quotation::class;
$dateFormat     = get_option('date_format');
$business       = $record->getBusiness();
$paymentStatus  = $isQuotation ? $record->getMeta('status') : $record->getPaymentStatus();
$isRecurring    = $isQuotation ? null : $record->isRecurring();
$currency       = $record->getCurrency();
$subtotal       = $record->getSubtotal();
$total          = $record->getTotal();
$notes          = $record->getNotes();
$previewLink    = get_site_url() . ($isQuotation ? '/invoize-quotation/' : '/invoize-preview/') . $record->getToken();


?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="font-family: 'DejaVu Sans', sans-serif;">
  <div style="min-height: 900px;">
    <!-- Header -->
    <table style="width: 100%;">
      <tr>
        <!-- Logo -->
        <td rowspan="2" style="width: 50%; padding: 0; margin: 0;">
          <?php if ($business['logo'] != 'false') : ?>
            <img src="<?php echo esc_url($business['logo']) ?>" alt="logo" height="70px" style="padding: 0; margin: 0;" />
          <?php endif ?>
        </td>
        <!-- Title -->
        <td style="width: 50%; text-align: right; font-size: 32px; font-weight: bold; padding: 0; margin: 0;">
          <?php echo esc_html(strtoupper($page)) ?>
        </td>
      </tr>

      <!-- Invoice number -->
      <?php if ($page == Invoice::INVOICE) : ?>
        <tr>
          <td style="text-align: right; font-size: 20px; padding: 0; margin: 0; padding: 0; margin: 0;">
            <?php echo esc_html($record->getInvoiceNumber()) ?>
          </td>
        </tr>

      <?php elseif ($page == 'quotation'): ?>
        <tr>
          <td style="text-align: right; font-size: 20px; padding: 0; margin: 0;">
            <?php echo esc_html($record->post_title) ?>
          </td>
        </tr>
        <tr>
          <td style="text-align: right; font-size: 14px; padding: 0; margin: 0;">
            <?php echo esc_html($record->getQuotationNumber()) ?>
          </td>
        </tr>

        <!-- Invoice number & Receipt number -->
      <?php elseif ($page == Invoice::RECEIPT) : ?>
        <tr>
          <td style="text-align: right; font-size: 20px; padding: 0; margin: 0;">
            <?php echo esc_html($record->receipt->getReceiptNumber()); ?>
          </td>
        </tr>
        <tr>
          <td colspan="3" style="text-align: right; font-size: 14px; padding: 0; margin: 0;">
            <?php echo esc_html($record->getInvoiceNumber()) ?>
          </td>
        </tr>
      <?php endif ?>
    </table>

    <div style="height: 1px; width: 90%; margin: 12px auto; background-color: #e2e8f0;"></div>

    <!-- Content Top -->
    <table style="width: 100%; font-size: 14px; margin-bottom: 8px;">
      <tr>
        <td style="width: 50%; font-size: 14px; font-weight: bold; padding: 0; margin: 0;">Issued by</td>
      </tr>

      <tr>
        <td style="width: 50%; font-size: 14px; padding: 0; margin: 0;"><?php echo esc_html($business['business_name']) ?></td>
      </tr>

      <tr>
        <td style="width: 50%; font-size: 12px; color: #64748b; padding: 0; margin: 0;"><?php echo esc_html($business['email']) ?></td>
        <!-- Status -->
        <td style="width: 7.5%; padding: 0; margin: 0;"></td>
        <td style="width: 17.5%; font-size: 14px; padding: 0; margin: 0;">Status</td>
        <td style="width: 2%; text-align: center; padding: 0; margin: 0;">:</td>
        <td style="width: 23%; text-align: right; padding: 0; margin: 0;">
          <table style="width: 100%; border-collapse: collapse;">
            <tr>
              <td style="width: 30%; padding: 0; margin: 0;"></td>
              <?php if ($isQuotation): ?>
                <td style="width: 70%; border-radius: 5px; color: white; background-color: <?php echo esc_attr($paymentStatus != 'active' ?  'gray;' : '#16a34a;') ?> font-weight: bold; text-align: center; padding: 0; margin: 0;">
                  <?php echo esc_html(strtoupper($paymentStatus)) ?>
                </td>
              <?php else: ?>
                <td style="width: 70%; border-radius: 5px; color: white; background-color: <?php echo esc_attr($paymentStatus == Invoice::UNPAID ?  '#ef4444;' : '#16a34a;') ?> font-weight: bold; text-align: center; padding: 0; margin: 0;">
                  <?php echo esc_html(strtoupper($paymentStatus)) ?>
                </td>
              <?php endif; ?>
            </tr>
          </table>
        </td>
      </tr>

      <?php if (!$isQuotation) : ?>
        <tr>
          <td style="width: 50%; font-size: 12px; color: #64748b; padding: 0; margin: 0;">
            <?php echo esc_html($business['phone_number']) ?>
          </td>
          <!-- Type -->
          <td style="width: 7.5%; padding: 0; margin: 0;"></td>
          <td style="width: 17.5%; padding: 0; margin: 0;">Type</td>
          <td style="width: 2%; text-align: center; padding: 0; margin: 0;">:</td>
          <td style="width: 23%; text-align: right; padding: 0; margin: 0;">
            <table style="width: 100%; border-collapse: collapse;">
              <tr>
                <td style="width: 30%; padding: 0; margin: 0;"></td>
                <td style="width: 70%; font-weight: bold; <?php echo esc_attr($isRecurring ? 'color: #3b82f6;' : 'color: #f59e0b;') ?> text-align: center; padding: 0; margin: 0;">
                  <?php echo esc_html($isRecurring ? 'Recurring' : 'One-time') ?>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      <?php endif; ?>

      <?php if (isset($business['website'])) : ?>
        <tr>
          <td style="width: 50%; font-size: 12px; color: #64748b; padding: 0; margin: 0;">
            <?php echo esc_html($business['website']) ?>
          </td>
        </tr>
      <?php endif ?>

      <?php if (isset($business['address'])) : ?>
        <tr>
          <td style="width: 50%; font-size: 12px; color: #64748b; padding: 0; margin: 0;">
            <?php echo wp_kses_post(nl2br($business['address'])) ?>
          </td>
        </tr>
      <?php endif ?>
    </table>

    <?php $customer = $record->getClient(); ?>
    <table style="width: 100%; font-size: 14px;margin-bottom:14px;">
      <tr>
        <td style="width: 50%; font-size: 14px; font-weight: bold; padding: 0; margin: 0;">
          <?php if ($record->isBilledToSameAsClient()): ?>
            Customer
          <?php else: ?>
            Bill to
          <?php endif; ?>
        </td>
        <?php if (!$isQuotation): ?>
          <!-- Order date -->
          <td style="width: 7.5%; padding: 0; margin: 0;"></td>
          <td style="width: 17.5%; padding: 0; margin: 0;">Order date</td>
          <td style="width: 2%; text-align: center; padding: 0; margin: 0;">:</td>
          <td style="width: 23%; text-align: right; padding: 0; margin: 0;">
            <?php echo esc_html(invoizeFormatDate($record->getOrderDate(), $dateFormat)); ?>
          </td>
        <?php endif; ?>
      </tr>

      <tr>
        <td style="width: 50%; font-size: 14px; padding: 0; margin: 0;">
          <?php if ($record->isBilledToSameAsClient()): ?>
            <?php echo esc_html($customer['name']) ?>
          <?php else: ?>
            <?php echo esc_html($record->getBilledTo()['name']); ?>
          <?php endif; ?>
        </td>
        <!-- Invoice date -->
        <td style="width: 7.5%; padding: 0; margin: 0;"></td>
        <td style="width: 17.5%; padding: 0; margin: 0;">
          <?php if ($isQuotation): ?>
            Quotation Date
          <?php else: ?>
            Invoice date
          <?php endif; ?>
        </td>
        <td style="width: 2%; text-align: center; padding: 0; margin: 0;">:</td>
        <td style="width: 23%; text-align: right; padding: 0; margin: 0;">
          <?php if ($isQuotation): ?>
            <?php echo esc_html(invoizeFormatDate($record->getQuotationDate(), $dateFormat)) ?>
          <?php else: ?>
            <?php echo esc_html(invoizeFormatDate($record->getInvoiceDate(), $dateFormat)) ?>
          <?php endif; ?>
        </td>
      </tr>

      <tr>
        <td style="width: 50%; font-size: 12px; color: #64748b; padding: 0; margin: 0;">
          <?php if ($record->isBilledToSameAsClient()): ?>
            <?php echo esc_html($customer['email']) ?>
          <?php else: ?>
            <?php echo nl2br(wp_kses_post($record->getBilledTo()['detail'])); ?>
          <?php endif; ?>
        </td>
        <!-- Due date -->
        <td style="width: 7.5%; padding: 0; margin: 0;"></td>
        <td style="width: 12.5%; padding: 0; margin: 0;">Due date</td>
        <td style="width: 2%; text-align: center; padding: 0; margin: 0;">:</td>
        <td style="width: 23%; text-align: right; padding: 0; margin: 0;">
          <?php echo esc_html(invoizeFormatDate($record->getDueDate(), $dateFormat)) ?>
        </td>
      </tr>

      <tr>
        <td style="width: 50%; font-size: 12px; color: #64748b; padding: 0; margin: 0;">
          <?php if ($record->isBilledToSameAsClient()): ?>
            <?php echo wp_kses_post(isset($customer['customAddress'])
              ? nl2br($customer['customAddress'])
              : nl2br($customer['address'])) ?>
          <?php endif; ?>
        </td>
        <?php if ($paymentStatus == Invoice::PAID) : ?>
          <!-- Paid date -->
          <td style="width: 7.5%; padding: 0; margin: 0;"></td>
          <td style="width: 17.5%; padding: 0; margin: 0;">Paid on</td>
          <td style="width: 2%; text-align: center; padding: 0; margin: 0;">:</td>
          <td style="width: 23%; text-align: right; padding: 0; margin: 0;">
            <?php echo esc_html(invoizeFormatDate($record->getPaidDate(), $dateFormat)); ?>
          </td>
        <?php endif ?>
      </tr>

      <tr>
        <td style="width: 50%; font-size: 12px; color: #64748b; padding: 0; margin: 0;">

        </td>
        <?php if ($paymentStatus == Invoice::PAID) : ?>
          <!-- Paid date -->
          <td style="width: 7.5%; padding: 0; margin: 0;"></td>
          <td style="width: 17.5%; padding: 0; margin: 0;">Paid using</td>
          <td style="width: 2%; text-align: center; padding: 0; margin: 0;">:</td>
          <td style="width: 23%; text-align: right; padding: 0; margin: 0;">
            <?php echo esc_html($record->getPaidMethod() ?: '-'); ?>
          </td>
        <?php endif ?>
      </tr>
    </table>

    <!-- Content Middle -->
    <?php $products = $record->getProducts(); ?>
    <?php $currency = $record->getCurrency(); ?>
    <table style="width: 100%; margin: 36px 0 0; border-collapse: collapse;">
      <!-- This is used to maintain the size of each cell -->
      <tr>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
        <td style="width: 8.3%; padding: 0; margin: 0;"></td>
      </tr>

      <thead>
        <tr style="background-color: #f1f5f9;">
          <th style="font-size: 14px; text-align: center; height: 40px; color: #64748b; padding: 0; margin: 0;">
            #
          </th>
          <th style="font-size: 14px; color: #64748b; padding: 0; margin: 0;" colspan="6">
            Products
          </th>
          <th style="font-size: 14px; text-align: center; color: #64748b; padding: 0; margin: 0;" colspan="2">
            Unit Price
            </th=>
          <th style="font-size: 14px; text-align: center; color: #64748b; padding: 0; margin: 0;">
            Qty
          </th>
          <th style="font-size: 14px; text-align: right; color: #64748b; padding: 0 10px 0 0; margin: 0;" colspan="2">
            Amount
          </th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($products as $index => $product) : ?>
          <tr>
            <td style="text-align: center; vertical-align: baseline; height: 36px; font-size: 14px; padding: 0; margin: 0;">
              <?php echo esc_html($index + 1) ?>
            </td>
            <td style="padding-left: 8px; font-size: 14px; vertical-align: baseline; padding-right: 0; margin: 0;" colspan="6">
              <?php if (isset($product['note'])) : ?>
                <table style="border-collapse: collapse; width: 100%;">
                  <tr>
                    <!-- str_replace \u{200B} is to remove zero width space.
                      Sometimes the product name is like this:
                      \xe2\x80\x8b OJS Hosting Package\xe2\x80\x8b 
                      so we remove the weird syntax -->
                    <td style="padding: 0; margin: 0;"><?php echo esc_html(str_replace("\u{200B}", "", $product['name'])) ?></td>
                  </tr>
                  <tr>
                    <td style="font-size: 12px; color: #64748b; padding: 0; margin: 0;"><?php echo wp_kses_post(nl2br($product['note'])) ?></td>
                  </tr>
                </table>
              <?php elseif (isset($product['description'])) : ?>
                <table style="border-collapse: collapse; width: 100%;">
                  <tr>
                    <td style="padding: 0; margin: 0;"><?php echo esc_html(str_replace("\u{200B}", "", $product['name'])) ?></td>
                  </tr>
                  <tr>
                    <td style="font-size: 12px; color: #64748b; padding: 0; margin: 0;"><?php echo wp_kses_post(nl2br($product['description'])) ?></td>
                  </tr>
                </table>
              <?php else: ?>
                <?php echo esc_html(str_replace("\u{200B}", "", $product['name'])) ?>
              <?php endif ?>
            </td>
            <td colspan="2" style="text-align: center; font-size: 14px; vertical-align: middle; padding: 0; margin: 0;">
              <?php echo esc_html(invoizeFormatCurrency($currency['name'], $product['unitPrice']))  ?>
            </td>
            <td style="text-align: center; font-size: 14px; vertical-align: middle; padding: 0; margin: 0;">
              <?php echo esc_html(invoizeFormatNumber($currency['name'], $product['quantity'])) ?>
            </td>
            <td colspan="2" style="text-align: right; font-size: 14px; vertical-align: middle; padding: 0 10px 0 0; margin: 0;">
              <?php echo esc_html(invoizeFormatCurrency($currency['name'], $product['amount']))  ?>
            </td>
          </tr>

        <?php endforeach ?>
      </tbody>
    </table>
    <div style="background-color: #f1f5f9; height: 2px;"></div>

    <!-- Content Bottom -->
    <table style="width: 100%; font-size: 12px; margin-top: 30px; border-collapse: collapse;">
      <tr>
        <!-- Payments -->
        <td style="width: 50%; vertical-align: baseline; padding: 0; margin: 0;">
          <table style="border-collapse: collapse;">
            <tr>
              <td colspan="3" style="font-size: 14px; font-weight: bold; padding-bottom: 8px; white-space: nowrap; padding-top: 0; padding-left: 0; padding-right: 0; margin: 0;">
                Payment Method
              </td>
            </tr>
            <?php $payments = $record->getPayments(); ?>

            <?php
            /**
             * if payment page are enabled, then only show banks.
             */
            $payments = array_filter($payments, function ($payment) {

              if (invoizeGetOption('payment.enablePaymentPage', false)) {
                return $payment['method'] == Payment::BANK || ($payment['method'] == Payment::PAYPAL && $payment['type'] == Payment::DIRECT_PAYMENT);
              }

              return true;
            });
            ?>

            <?php foreach ($payments as $index => $payment) : ?>
              <!-- Separator -->
              <?php if (count($payments) > 1 && $index > 0) : ?>
                <tr>
                  <td style="height: 12px; padding: 0; margin: 0;"></td>
                </tr>
              <?php endif ?>

              <tr>
                <td colspan="3" style="font-size: 12px; font-weight: bold; vertical-align: baseline; padding: 0; margin: 0;">
                  <?php echo esc_html(ucfirst($payment['method'])) ?>
                </td>
              </tr>

              <!-- Bank Payment -->
              <?php if ($payment['method'] == Payment::BANK) : ?>
                <tr style="vertical-align: baseline;">
                  <td style="padding: 0; margin: 0;">Name</td>
                  <td style="width: 10px; text-align: center; padding: 0; margin: 0;">:</td>
                  <td style="padding: 0; margin: 0;"><?php echo esc_html(ucfirst($payment['name'])) ?></td>
                </tr>
                <tr style="vertical-align: baseline;">
                  <td style="padding: 0; margin: 0;">Detail</td>
                  <td style="width: 10px; text-align: center; padding: 0; margin: 0;">:</td>
                  <td style="padding: 0; margin: 0;"><?php echo wp_kses_post(nl2br($payment['detail'])) ?></td>
                </tr>
              <?php endif; ?>

              <!-- Paypal Auto Confirmation -->
              <?php if ($payment['method'] == Payment::PAYPAL && $payment['type'] == Payment::AUTO_CONFIRMATION) : ?>
                <?php
                $paypalAutoLink = isset($payment['checkout']['links'])
                  ? array_values(array_filter($payment['checkout']['links'], fn($link) => $link['rel'] == 'payer-action'))
                  : null;
                $paypalAutoLink = $paypalAutoLink && isset($paypalAutoLink[0]['href'])
                  ? $paypalAutoLink[0]['href']
                  : null;
                ?>
                <tr style="vertical-align: baseline;">
                  <td style="padding: 0; margin: 0;">Method</td>
                  <td style="padding: 0; margin: 0;">:</td>
                  <td style="padding: 0; margin: 0;"><?php echo esc_html(ucfirst($payment['type'])) ?></td>
                </tr>
                <tr style="vertical-align: baseline;">
                  <td style="padding: 0; margin: 0;">Name</td>
                  <td style="padding: 0; margin: 0;">:</td>
                  <td style="padding: 0; margin: 0;"><?php echo esc_html($payment['name']) ?></td>
                </tr>
                <?php if ($paypalAutoLink && $paymentStatus == Invoice::UNPAID) : ?>
                  <tr style="vertical-align: baseline;">
                    <td style="padding: 0; margin: 0;">Link</td>
                    <td style="padding: 0; margin: 0;">:</td>
                    <td style="padding: 0; margin: 0;">
                      <a href="<?php echo esc_url($paypalAutoLink) ?>" target="_self" style="color: #2563eb; text-decoration: none;">
                        <?php echo esc_url($paypalAutoLink); ?>
                      </a>
                    </td>
                  </tr>
                <?php endif ?>
                <?php if (!$paypalAutoLink && !$isRecurring) : ?>
                  <tr>
                    <td style="color: #ef4444; padding: 0; margin: 0;">Payment link unavailable</td>
                  </tr>
                <?php endif ?>
              <?php endif ?>

              <!-- Paypal Direct Payment -->
              <?php if ($payment['method'] == Payment::PAYPAL && $payment['type'] == Payment::DIRECT_PAYMENT) : ?>
                <tr style="vertical-align: baseline;">
                  <td style="padding: 0; margin: 0;">Method</td>
                  <td style="padding: 0; margin: 0;">:</td>
                  <td style="padding: 0; margin: 0;"><?php echo esc_html(ucfirst($payment['type'])) ?></td>
                </tr>
                <?php if ($paymentStatus == Invoice::UNPAID) : ?>
                  <tr style="vertical-align: baseline;">
                    <td style="padding: 0; margin: 0;">Link</td>
                    <td style="padding: 0; margin: 0;">:</td>
                    <td style="padding: 0; margin: 0;">
                      <a href="<?php echo esc_url($payment['name']) ?>" target="_self" style="color: #2563eb; text-decoration: none;">
                        <?php echo esc_url($payment['name']); ?>
                      </a>
                    </td>
                  </tr>
                <?php endif ?>
              <?php endif ?>

              <!-- Xendit -->
              <?php if ($payment['method'] == Payment::XENDIT) : ?>
                <?php
                $xenditLink = isset($payment['checkout']['invoice_url'])
                  ? $payment['checkout']['invoice_url']
                  : null;
                ?>
                <div style="padding: 0; margin: 0;">(Credit card/other payment method)</div>
                <?php if ($xenditLink || !$isRecurring) : ?>
                  <tr style="vertical-align: baseline;">
                    <td style="padding: 0; margin: 0;">Total</td>
                    <td style="padding: 0; margin: 0;">:</td>
                    <td style="padding: 0; margin: 0;"><?php echo esc_html(invoizeFormatCurrency("IDR", $payment['total'])) ?></td>
                  </tr>
                  <?php if ($xenditLink && $paymentStatus == Invoice::UNPAID) : ?>
                    <tr style="vertical-align: baseline;">
                      <td style="padding: 0; margin: 0;">Link</td>
                      <td style="padding: 0; margin: 0;">:</td>
                      <td style="padding: 0; margin: 0;">
                        <a href="<?php echo esc_url($xenditLink) ?>" target="_self" style="color: #2563eb; text-decoration: none;">
                          <?php echo esc_url($xenditLink); ?>
                        </a>
                      </td>
                    </tr>
                  <?php endif ?>
                <?php endif ?>
                <?php if (!$xenditLink && !$isRecurring) : ?>
                  <tr>
                    <td style="color: #ef4444; padding: 0; margin: 0;">Payment link unavailable</td>
                  </tr>
                <?php endif ?>
              <?php endif ?>

              <!-- Woocommerce -->
              <?php if ($payment['method'] == Payment::WOOCOMMERCE_TRANSACTION) : ?>
                <!-- Woocommerce Bank Transer -->
                <?php if (str_contains($payment['name'], 'Direct bank transfer') || $payment['type'] == 'woocommerce bank') : ?>
                  <?php foreach ($payment['detail'] as $index => $detail) : ?>
                    <!-- Separator -->
                    <?php if (count($payment['detail']) > 1 && $index > 0) : ?>
                      <tr>
                        <td style="height: 4px; padding: 0; margin: 0;"></td>
                      </tr>
                      <tr>
                        <td colspan="3" style="height: 1px; background-color: #e2e8f0; padding: 0; margin: 0;"></td>
                      </tr>
                      <tr>
                        <td style="height: 4px; padding: 0; margin: 0;"></td>
                      </tr>
                    <?php endif ?>
                    <?php if (isset($detail['account_name'])) : ?>
                      <tr style="vertical-align: baseline;">
                        <td style="padding: 0; margin: 0;">Account name</td>
                        <td style="padding: 0; margin: 0;">:</td>
                        <td style="padding: 0; margin: 0;"><?php echo esc_html($detail['account_name']) ?></td>
                      </tr>
                    <?php endif ?>
                    <?php if (isset($detail['account_number'])) : ?>
                      <tr style="vertical-align: baseline;">
                        <td style="padding: 0; margin: 0;">Account number</td=>
                        <td style="padding: 0; margin: 0;">:</td>
                        <td style="padding: 0; margin: 0;"><?php echo esc_html($detail['account_number']) ?></td>
                      </tr>
                    <?php endif ?>
                    <?php if (isset($detail['bank_name'])) : ?>
                      <tr style="vertical-align: baseline;">
                        <td style="padding: 0; margin: 0;">Bank name</td=>
                        <td style="padding: 0; margin: 0;">:</td>
                        <td style="padding: 0; margin: 0;"><?php echo esc_html($detail['bank_name']) ?></td>
                      </tr>
                    <?php endif ?>
                    <?php if (isset($detail['sort_code'])) : ?>
                      <tr style="vertical-align: baseline;">
                        <td style="padding: 0; margin: 0;">Sort code</td>
                        <td style="padding: 0; margin: 0;">:</td>
                        <td style="padding: 0; margin: 0;"><?php echo esc_html($detail['sort_code']) ?></td>
                      </tr>
                    <?php endif ?>
                    <?php if (isset($detail['iban'])) : ?>
                      <tr style="vertical-align: baseline;">
                        <td style="padding: 0; margin: 0;">IBAN</td>
                        <td style="padding: 0; margin: 0;">:</td>
                        <td style="padding: 0; margin: 0;"><?php echo esc_html($detail['iban']) ?></td>
                      </tr>
                    <?php endif ?>
                    <?php if (isset($detail['bic'])) : ?>
                      <tr style="vertical-align: baseline;">
                        <td style="padding: 0; margin: 0;">BIC/Swift</td>
                        <td style="padding: 0; margin: 0;">:</td>
                        <td style="padding: 0; margin: 0;"><?php echo esc_html($detail['bic']) ?></td>
                      </tr>
                    <?php endif ?>
                  <?php endforeach ?>

                  <!-- Woocommerce Paypal -->
                <?php elseif ($payment['name'] == 'woocommerce paypal' || $payment['type'] == 'woocommerce paypal') : ?>
                  <tr style="vertical-align: baseline;">
                    <td style="padding: 0; margin: 0;">Method</td>
                    <td style="padding: 0; margin: 0;">:</td>
                    <td style="padding: 0; margin: 0;"><?php echo esc_html($payment['name']) ?></td>
                  </tr>
                  <tr style="vertical-align: baseline;">
                    <td style="padding: 0; margin: 0;">Detail</td>
                    <td style="padding: 0; margin: 0;">:</td>
                    <td style="padding: 0; margin: 0;"><?php echo esc_html($payment['detail']) ?></td>
                  </tr>

                  <!-- Woocommerce other payment -->
                <?php else : ?>
                  <?php $wcPaymentLink = $payment['checkout'] ?? null; ?>
                  <tr style="vertical-align: baseline;">
                    <td style="padding: 0; margin: 0;">Method</td>
                    <td style="padding: 0; margin: 0;">:</td>
                    <td style="padding: 0; margin: 0;"><?php echo esc_html($payment['name']) ?></td>
                  </tr>
                  <tr style="vertical-align: baseline;">
                    <td style="padding: 0; margin: 0;">Detail</td>
                    <td style="padding: 0; margin: 0;">:</td>
                    <td style="color: #2563eb; text-decoration: none; padding: 0; margin: 0;">
                      <a href="<?php echo esc_url($wcPaymentLink) ?>" target="_self">
                        <?php echo esc_url($wcPaymentLink) ?>
                      </a>
                    </td>
                  </tr>
                <?php endif ?>
              <?php endif; ?>

            <?php endforeach ?>

            <?php if (invoizeGetOption('payment.enablePaymentPage') && !$isQuotation) : ?>
              <tr>
                <td style="height: 12px; padding: 0; margin: 0;"></td>
              </tr>
              <tr>
                <td colspan="3" style="font-size: 12px; font-weight: bold; vertical-align: baseline; padding: 0; margin: 0;">
                  Payment Link <span style="color:#64748b; font-size: 11px; font-weight: normal;">(Other payment method available here)</span>
                </td>
              </tr>
              <tr style="vertical-align: baseline;">
                <td colspan="3" style="padding: 0; margin: 0;">
                  <a href="<?php echo esc_url($record->getPaymentLink()); ?>" target="_blank">
                    <?php echo esc_url($record->getPaymentLink()); ?>
                  </a>
                </td>
              </tr>
            <?php endif; ?>
          </table>
        </td>

        <!-- Summary -->
        <td style="width: 50%; vertical-align: baseline; padding: 0; margin: 0;">
          <table style="width: 100%;">
            <!-- Subtotal  -->
            <tr>
              <td style="width: 15%; padding: 0; margin: 0;"></td>
              <td style="width: 35%; font-size: 14px; font-weight: bold; padding: 0; margin: 0;">Subtotal</td>
              <td style="width: 50%; font-size: 14px; text-align: right; padding: 0; margin: 0;">
                <?php echo esc_html(invoizeFormatCurrency($currency['name'], $subtotal)) ?>
              </td>
            </tr>

            <tr>
              <td style="height: 8px; padding: 0; margin: 0;"></td>
            </tr>

            <!-- Discount -->
            <?php $discounts = $record->getDiscounts(); ?>
            <?php foreach ($discounts['data'] as $discount) : ?>
              <?php $discountValue = $discount['type'] == 'percent'
                ? ($discount['value'] / 100) * $subtotal
                : $discount['value'];
              ?>
              <tr style="width: 100%; color: #dc2626;">
                <td style="width: 15%; padding: 0; margin: 0;"></td>
                <td style="width: 50%; font-size: 14px; padding: 0; margin: 0;">
                  <?php echo esc_html($discount['name'] . ' ' .  ($discount['type'] == 'percent'
                    ? $discount['value'] . '%'
                    : '')) ?>
                </td>
                <td style="width: 50%; font-size: 14px; text-align: right; padding: 0; margin: 0;">
                  - <?php echo esc_html(invoizeFormatCurrency($currency['name'], $discountValue)) ?>
                </td>
              </tr>
            <?php endforeach ?>

            <?php if (count($discounts['data']) > 0) : ?>
              <tr>
                <td style="height: 8px; padding: 0; margin: 0;"></td>
              </tr>
            <?php endif ?>

            <!-- Tax -->
            <?php $taxes = $record->getTaxes(); ?>
            <?php foreach ($taxes['data'] as $tax) : ?>
              <?php $taxValue = $tax['type'] == 'percent'
                ? ($tax['value'] / 100) * ($subtotal - $discounts['total'])
                : $tax['value'];
              ?>
              <tr>
                <td style="width: 15%; padding: 0; margin: 0;"></td>
                <td style="width: 50%; font-size: 14px; padding: 0; margin: 0;">
                  <?php echo esc_html($tax['name'] . ' ' . ($tax['type'] == 'percent'
                    ? $tax['value'] . '%'
                    : '')); ?>
                </td>
                <td style="width: 50%; font-size: 14px; text-align: right; padding: 0; margin: 0;">
                  <?php echo esc_html(invoizeFormatCurrency($currency['name'], $taxValue)) ?>
                </td>
              </tr>
            <?php endforeach ?>

            <?php if (count($taxes['data']) > 0) : ?>
              <tr>
                <td style="height: 8px; padding: 0; margin: 0;"></td>
              </tr>
            <?php endif ?>

            <!-- Total -->
            <tr>
              <td style="width: 15%; padding: 0; margin: 0;"></td>
              <td style="width: 35%; font-weight: bold; font-size: 16px; padding: 0; margin: 0;">Total</td>
              <td style="width: 50%; font-weight: bold; font-size: 16px; text-align: right; padding: 0; margin: 0;">
                <?php echo esc_html(invoizeFormatCurrency($currency['name'], $total))  ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <div style="height: 40px;"></div>

    <table style="width: 100%; border-collapse: collapse;">
      <!-- Note -->
      <tr style="width: 100%;">
        <td colspan="2" style="padding: 0; margin: 0;">
          <table style="width: 100%;">
            <tr style="width: 100%;">
              <td style="font-size: 14px; font-weight: bold; padding: 0; margin: 0;">Note</td>
            </tr>
            <tr>
              <td style="font-size: 12px; page-break-inside: initial; padding: 0; margin: 0;">
                <?php echo wp_kses_post(!empty($notes['note']) ? nl2br($notes['note']) : '-') ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr style="width: 100%;">
        <!-- Terms & Conditions -->
        <td style="width: 80%; padding: 0; margin: 0;">
          <table>
            <tr>
              <td style="font-size: 14px; font-weight: bold; padding: 0; margin: 0;">Terms & Conditions</td>
            </tr>
            <tr>
              <td style="font-size: 12px; padding: 0; margin: 0;">
                <?php echo wp_kses_post(!empty($notes['terms']) ? nl2br($notes['terms']) : '-') ?>
              </td>
            </tr>
          </table>
        </td>
        <!-- QR Code -->
        <td style="text-align: right; padding: 0; margin: 0;">
          <img src="<?php echo wp_kses_post((new QRCode())->render($previewLink)) ?>" alt="QR Code" width="96px">
        </td>
      </tr>

      <!-- Footer link -->
      <tr>
        <td colspan="2" style="text-align: right; font-size: 12px; padding-top: 20px; padding-bottom: 0; padding-left: 0; padding-right: 0; margin: 0;">
          <a href="<?php echo esc_url($previewLink) ?>" style="text-decoration: none; color: #64748b; font-style: italic;">
            <?php echo esc_url($previewLink) ?>
          </a>
        </td>
      </tr>
      <?php if (!invoize()->is_paying_or_trial()): ?>
        <tr>
          <td style="text-align: left; font-size: 12px; padding-top: 20px; padding-bottom: 0; padding-left: 0; padding-right: 0; margin: 0; color:#ddd">
            Invoize by <a href="https://wpsora.com" target="_blank">wpsora.com</a>
          </td>
        </tr>
      <?php endif; ?>
    </table>
  </div>

</body>

</html>