<?php
defined('ABSPATH') or die('No script kiddies please!');

use chillerlan\QRCode\QRCode;
use Invoize\Models\Invoice;
use Invoize\Models\Payment;

$dateFormat     = get_option('date_format');
$business       = $invoice->getBusiness();
$paymentStatus  = $invoice->getPaymentStatus();
$isRecurring    = $invoice->isRecurring();
$currency       = $invoice->getCurrency();
$subtotal       = $invoice->getSubtotal();
$total          = $invoice->getTotal();
$notes          = $invoice->getNotes();
$previewLink    = get_site_url() . '/invoize-preview/' . $invoice->getToken();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php
  $syling = '
      html {
        margin: 4px;
      }
      body {
        font-family: "DejaVu Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
        height: auto;
      }
  ';
  ?>
</head>

<body>
  <!-- Header -->
  <table style="width: 100%;">
    <tr>
      <td style="text-align: center;">
        <?php if ($business['logo'] != 'false') : ?>
          <img src="<?php echo esc_url($business['logo']) ?>" alt="logo" height="20px" />
        <?php endif ?>
      </td>
      <td style="text-align: center; font-weight: bold; font-size: 14px;">
        <?php echo esc_html(strtoupper($page)) ?>
      </td>
    </tr>
  </table>

  <div style="height: 1px; width: 100%; margin: 3px auto; background-color: black;"></div>

  <table style="width: 100%; font-size: 8px;">
    <tr>
      <td>Status</td>
      <td>:</td>
      <td style="text-align: center; color: white; background-color: <?php echo $paymentStatus == Invoice::UNPAID ?  '#ef4444;' : '#16a34a;' ?> font-weight: bold;">
        <?php echo esc_html(strtoupper($invoice->getPaymentStatus())) ?>
      </td>
    </tr>
    <tr>
      <td>Type</td>
      <td>:</td>
      <td style="text-align: center; font-weight: bold; <?php echo esc_attr($isRecurring ? 'color: #3b82f6;' : 'color: #f59e0b;') ?>">
        <?php echo esc_html($isRecurring ? 'Recurring' : 'One-time') ?>
      </td>
    </tr>
    <tr>
      <td>Order Date</td>
      <td>:</td>
      <td style="text-align: center;"><?php echo esc_html(invoizeFormatDate($invoice->getOrderDate(), $dateFormat)); ?></td>
    </tr>
    <tr>
      <td>Invoice Date</td>
      <td>:</td>
      <td style="text-align: center;"><?php echo esc_html(invoizeFormatDate($invoice->getInvoiceDate(), $dateFormat)); ?></td>
    </tr>
    <?php if ($paymentStatus == Invoice::UNPAID): ?>
      <tr>
        <td>Due Date</td>
        <td>:</td>
        <td style="text-align: center;"><?php echo esc_html(invoizeFormatDate($invoice->getDueDate(), $dateFormat)); ?></td>
      </tr>
    <?php elseif ($paymentStatus == Invoice::PAID): ?>
      <tr>
        <td>Paid Date</td>
        <td>:</td>
        <td style="text-align: center;"><?php echo esc_html(invoizeFormatDate($invoice->getPaidDate(), $dateFormat)); ?></td>
      </tr>
    <?php endif ?>
  </table>

  <div style="height: 1px; width: 100%; margin: 3px auto; background-color: black;"></div>

  <!-- Content Top -->
  <table style="width: 100%; font-size: 8px; color: #64748b; margin-bottom: 8px; border-collapse: collapse;">
    <tr style="font-size: 10px; color: black;">
      <td><?php echo esc_html($business['business_name']) ?></td>
    </tr>
    <tr>
      <td><?php echo esc_html($business['email']) ?></td>
    </tr>
    <tr>
      <td><?php echo esc_html($business['phone_number']) ?></td>
    </tr>
    <?php if (isset($business['website'])) : ?>
      <tr>
        <td>
          <?php echo esc_html($business['website']) ?>
        </td>
      </tr>
    <?php endif ?>
    <?php if (isset($business['address'])) : ?>
      <tr>
        <td>
          <?php echo wp_kses_post(nl2br($business['address'])) ?>
        </td>
      </tr>
    <?php endif ?>
  </table>

  <?php $customer = $invoice->getClient(); ?>
  <table style="width: 100%; font-size: 8px; color: #64748b; margin-bottom: 8px; border-collapse: collapse;">
    <tr>
      <td style="font-size: 10px; color: black;">Customer</td>
    </tr>
    <tr>
      <td><?php echo esc_html($customer['name']) ?></td>
    </tr>
    <tr>
      <td><?php echo esc_html($customer['email']) ?></td>
    </tr>
    <tr>
      <td>
        <?php echo wp_kses_post(isset($customer['customAddress'])
          ? nl2br($customer['customAddress'])
          : nl2br($customer['address'])) ?>
      </td>
    </tr>
  </table>

  <div style="background-color: black; height: 1px; margin-bottom: 1px"></div>
  <div style="background-color: black; height: 1px;"></div>

  <!-- Content Middle -->
  <?php $products = $invoice->getProducts(); ?>
  <?php $currency = $invoice->getCurrency(); ?>
  <?php foreach ($products as $index => $product): ?>
    <table style="width: 100%; font-size: 8px;">
      <tr>
        <td style="width: 40%;"></td>
        <td style="width: 5%;"></td>
        <td style="width: 10%;"></td>
        <td style="width: 5%;"></td>
        <td style="width: 40%;"></td>
      </tr>
      <tr style="width: 100%;">
        <!-- str_replace \u{200B} is to remove zero width space.
          Sometimes the product name is like this:
          \xe2\x80\x8b OJS Hosting Package\xe2\x80\x8b
          so we remove the weird syntax -->
        <td colspan="5" style="font-weight: 500;">
          <?php echo esc_html(str_replace("\u{200B}", "", $product['name'])) ?>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo esc_html(invoizeFormatCurrency($currency['name'], $product['unitPrice']))  ?>
        </td>
        <td>X</td>
        <td><?php echo esc_html(invoizeFormatNumber($currency['name'], $product['quantity'])) ?></td>
        <td>=</td>
        <td style="text-align: right;">
          <?php echo esc_html(invoizeFormatCurrency($currency['name'], $product['amount']))  ?>
        </td>
      </tr>
    </table>
  <?php endforeach ?>

  <div style="background-color: black; height: 1px; margin-top: 8px; margin-bottom: 1px;"></div>
  <div style="background-color: black; height: 1px; margin-bottom: 8px;"></div>

  <!-- Content Bottom -->
  <!-- Summary -->
  <table style="width: 100%; font-size: 8px;">
    <!-- Subtotal  -->
    <tr>
      <td>Subtotal</td>
      <td style="text-align: right;">
        <?php echo esc_html(invoizeFormatCurrency($currency['name'], $subtotal)) ?>
      </td>
    </tr>

    <tr>
      <td style="height: 2px;"></td>
    </tr>

    <!-- Discount -->
    <?php $discounts = $invoice->getDiscounts(); ?>
    <?php foreach ($discounts['data'] as $discount) : ?>
      <?php $discountValue = $discount['type'] == 'percent'
        ? ($discount['value'] / 100) * $subtotal
        : $discount['value'];
      ?>
      <tr style="width: 100%; color: #dc2626;">
        <td>
          <?php echo esc_html($discount['name'] . ' ' .  ($discount['type'] == 'percent'
            ? $discount['value'] . '%'
            : '')) ?>
        </td>
        <td style="width: 50%; text-align: right;">
          - <?php echo esc_html(invoizeFormatCurrency($currency['name'], $discountValue)) ?>
        </td>
      </tr>
    <?php endforeach ?>

    <?php if (count($discounts['data']) > 0) : ?>
      <tr>
        <td style="height: 2px;"></td>
      </tr>
    <?php endif ?>

    <!-- Tax -->
    <?php $taxes = $invoice->getTaxes(); ?>
    <?php foreach ($taxes['data'] as $tax) : ?>
      <?php $taxValue = $tax['type'] == 'percent'
        ? ($tax['value'] / 100) * ($subtotal - $discounts['total'])
        : $tax['value'];
      ?>
      <tr>
        <td>
          <?php echo esc_html($tax['name'] . ' ' . ($tax['type'] == 'percent'
            ? $tax['value'] . '%'
            : '')); ?>
        </td>
        <td style="width: 50%; text-align: right;">
          <?php echo esc_html(invoizeFormatCurrency($currency['name'], $taxValue)) ?>
        </td>
      </tr>
    <?php endforeach ?>

    <?php if (count($taxes['data']) > 0) : ?>
      <tr>
        <td style="height: 2px;"></td>
      </tr>
    <?php endif ?>

    <!-- Total -->
    <tr>
      <td style="font-weight: bold; font-size: 10px;">Total</td>
      <td style="font-weight: bold; font-size: 10px; text-align: right;">
        <?php echo esc_html(formatCurrency($currency['name'], $total)) ?>
      </td>
    </tr>
  </table>

  <div style="background-color: black; height: 1px; margin: 8px 0"></div>

  <!-- Payments -->
  <table style="width: 100%; font-size: 8px; border-collapse: collapse;">
    <tr>
      <td style="width: 50%; vertical-align: baseline;">
        <table style="border-collapse: collapse;">
          <tr>
            <td colspan="3" style="font-size: 10px; padding-bottom: 2px; font-weight: 600; white-space: nowrap;">
              Payment Method
            </td>
          </tr>
          <?php $payments = $invoice->getPayments(); ?>
          <?php foreach ($payments as $index => $payment) : ?>
            <!-- Separator -->
            <?php if (count($payments) > 1 && $index > 0) : ?>
              <tr>
                <td style="height: 12px;"></td>
              </tr>
            <?php endif ?>

            <tr>
              <td colspan="3" style="font-size: 9px; font-weight: 600; vertical-align: baseline;">
                <?php echo esc_html(ucfirst($payment['method'])) ?>
              </td>
            </tr>

            <!-- Bank Payment -->
            <?php if ($payment['method'] == Payment::BANK) : ?>
              <tr style="vertical-align: baseline;">
                <td>Name</td>
                <td style="width: 10px; text-align: center;">:</td>
                <td><?php echo esc_html(ucfirst($payment['name'])) ?></td>
              </tr>
              <tr style="vertical-align: baseline;">
                <td>Detail</td>
                <td style="width: 10px; text-align: center;">:</td>
                <td><?php echo wp_kses_post(nl2br($payment['detail'])) ?></td>
              </tr>

              <!-- Paypal Auto Confirmation -->
            <?php elseif (
              $payment['method'] == Payment::PAYPAL
              && $payment['type'] == Payment::AUTO_CONFIRMATION
            ) : ?>
              <?php
              $paypalAutoLink = isset($payment['checkout']['links'])
                ? array_values(array_filter($payment['checkout']['links'], fn($link) => $link['rel'] == 'payer-action'))
                : null;
              $paypalAutoLink = $paypalAutoLink && isset($paypalAutoLink[0]['href'])
                ? $paypalAutoLink[0]['href']
                : null;
              ?>
              <tr style="vertical-align: baseline;">
                <td>Method</td>
                <td>:</td>
                <td><?php echo esc_html(ucfirst($payment['type'])) ?></td>
              </tr>
              <tr style="vertical-align: baseline;">
                <td>Name</td>
                <td>:</td>
                <td><?php echo esc_html($payment['name']) ?></td>
              </tr>
              <?php if ($paypalAutoLink && $paymentStatus == Invoice::UNPAID) : ?>
                <tr style="vertical-align: baseline;">
                  <td>Link</td>
                  <td>:</td>
                  <td>
                    <a href="<?php echo esc_url($paypalAutoLink) ?>" target="_self" style="color: #2563eb; text-decoration: none;">
                      <?php echo esc_url($paypalAutoLink); ?>
                    </a>
                  </td>
                </tr>
              <?php endif ?>
              <?php if (!$paypalAutoLink && !$isRecurring) : ?>
                <tr>
                  <td style="color: #ef4444;">Payment link unavailable</td>
                </tr>
              <?php endif ?>

              <!-- Paypal Direct Payment -->
            <?php elseif (
              $payment['method'] == Payment::PAYPAL
              && $payment['type'] == Payment::DIRECT_PAYMENT
            ) : ?>
              <tr style="vertical-align: baseline;">
                <td>Method</td>
                <td>:</td>
                <td><?php echo esc_html(ucfirst($payment['type'])) ?></td>
              </tr>
              <?php if ($paymentStatus == Invoice::PAID) : ?>
                <tr style="vertical-align: baseline;">
                  <td>Link</td>
                  <td>:</td>
                  <td>
                    <a href="<?php echo esc_url($payment['checkout']) ?>" target="_self" style="color: #2563eb; text-decoration: none;">
                      <?php echo esc_url($payment['checkout']) ?>
                    </a>
                  </td>
                </tr>
              <?php endif ?>

              <!-- Xendit -->
            <?php elseif ($payment['method'] == Payment::XENDIT) : ?>
              <?php
              $xenditLink = isset($payment['checkout']['invoice_url'])
                ? $payment['checkout']['invoice_url']
                : null;
              ?>
              <div>(Credit card/other payment method)</div>
              <?php if ($xenditLink || !$isRecurring) : ?>
                <tr style="vertical-align: baseline;">
                  <td>Total</td>
                  <td>:</td>
                  <td><?php echo esc_html(invoizeFormatCurrency("IDR", $payment['total'])) ?></td>
                </tr>
                <?php if ($xenditLink && $paymentStatus == Invoice::UNPAID) : ?>
                  <tr style="vertical-align: baseline;">
                    <td>Link</td>
                    <td>:</td>
                    <td>
                      <a href="<?php echo esc_url($xenditLink) ?>" target="_self" style="color: #2563eb; text-decoration: none;">
                        <?php echo esc_url($xenditLink); ?>
                      </a>
                    </td>
                  </tr>
                <?php endif ?>
              <?php endif ?>
              <?php if (!$xenditLink && !$isRecurring) : ?>
                <tr>
                  <td style="color: #ef4444;">Payment link unavailable</td>
                </tr>
              <?php endif ?>

              <!-- Woocommerce -->
            <?php elseif ($payment['method'] == Payment::WOOCOMMERCE_TRANSACTION) : ?>
              <!-- Woocommerce Bank Transer -->
              <?php if (
                str_contains($payment['name'], 'Direct bank transfer')
                || $payment['type'] == 'woocommerce bank'
              ) : ?>
                <?php foreach ($payment['detail'] as $index => $detail) : ?>
                  <!-- Separator -->
                  <?php if (count($payment['detail']) > 1 && $index > 0) : ?>
                    <tr>
                      <td style="height: 4px;"></td>
                    </tr>
                    <tr>
                      <td colspan="3" style="background-color: #e2e8f0;"></td>
                    </tr>
                    <tr>
                      <td style="height: 4px;"></td>
                    </tr>
                  <?php endif ?>
                  <?php if (isset($detail['account_name'])) : ?>
                    <tr style="vertical-align: baseline;">
                      <td>Account name</td>
                      <td>:</td>
                      <td><?php echo esc_html($detail['account_name']) ?></td>
                    </tr>
                  <?php endif ?>
                  <?php if (isset($detail['account_number'])) : ?>
                    <tr style="vertical-align: baseline;">
                      <td>Account number</td=>
                      <td>:</td>
                      <td><?php echo esc_html($detail['account_number']) ?></td>
                    </tr>
                  <?php endif ?>
                  <?php if (isset($detail['bank_name'])) : ?>
                    <tr style="vertical-align: baseline;">
                      <td>Bank name</td=>
                      <td>:</td>
                      <td><?php echo esc_html($detail['bank_name']) ?></td>
                    </tr>
                  <?php endif ?>
                  <?php if (isset($detail['sort_code'])) : ?>
                    <tr style="vertical-align: baseline;">
                      <td>Sort code</td>
                      <td>:</td>
                      <td><?php echo esc_html($detail['sort_code']) ?></td>
                    </tr>
                  <?php endif ?>
                  <?php if (isset($detail['iban'])) : ?>
                    <tr style="vertical-align: baseline;">
                      <td>IBAN</td>
                      <td>:</td>
                      <td><?php echo esc_html($detail['iban']) ?></td>
                    </tr>
                  <?php endif ?>
                  <?php if (isset($detail['bic'])) : ?>
                    <tr style="vertical-align: baseline;">
                      <td>BIC/Swift</td>
                      <td>:</td>
                      <td><?php echo esc_html($detail['bic']) ?></td>
                    </tr>
                  <?php endif ?>
                <?php endforeach ?>

                <!-- Woocommerce Paypal -->
              <?php elseif (
                $payment['name'] == 'woocommerce paypal'
                || $payment['type'] == 'woocommerce paypal'
              ) : ?>
                <tr style="vertical-align: baseline;">
                  <td>Method</td>
                  <td>:</td>
                  <td><?php echo esc_html($payment['name']) ?></td>
                </tr>
                <tr style="vertical-align: baseline;">
                  <td>Detail</td>
                  <td>:</td>
                  <td><?php echo esc_html($payment['detail']) ?></td>
                </tr>

                <!-- Woocommerce other payment -->
              <?php else : ?>
                <?php $wcPaymentLink = $payment['checkout'] ?? null; ?>
                <tr style="vertical-align: baseline;">
                  <td>Method</td>
                  <td>:</td>
                  <td><?php echo esc_html($payment['name']) ?></td>
                </tr>
                <tr style="vertical-align: baseline;">
                  <td>Detail</td>
                  <td>:</td>
                  <td style="color: #2563eb; text-decoration: none;">
                    <a href="<?php echo esc_url($wcPaymentLink) ?>" target="_self">
                      <?php echo esc_url($wcPaymentLink) ?>
                    </a>
                  </td>
                </tr>
              <?php endif ?>
            <?php endif ?>
          <?php endforeach ?>
        </table>
      </td>
    </tr>
  </table>

  <div style="background-color: black; height: 1px; margin: 8px 0;"></div>

  <table style="border-collapse: collapse; font-size: 8px;">
    <!-- Note -->
    <tr style="page-break-after: avoid;">
      <td style="font-size: 10px; font-weight: bold; ">Note</td>
    </tr>
    <tr style="page-break-inside: avoid;">
      <td>
        <?php echo wp_kses_post(!empty($notes['note']) ? nl2br($notes['note']) : '-') ?>
      </td>
    </tr>

    <!-- Terms & Conditions -->
    <tr style="page-break-after: avoid;">
      <td style="font-size: 10px; font-weight: bold;">Terms & Conditions</td>
    </tr>
    <tr style="page-break-inside: avoid; ">
      <td>
        <?php echo wp_kses_post(!empty($notes['terms']) ? nl2br($notes['terms']) : '-') ?>
      </td>
    </tr>

    <div style="height: 4px;"></div>

    <!-- QR Code -->
    <tr style="text-align: center; width: 100%;">
      <td style="width: 100%;">
        <img src="<?php echo wp_kses_post((new QRCode())->render($previewLink)) ?>" alt="QR Code" height="120px">
      </td>
    </tr>

    <!-- Footer link -->
    <tr>
      <td style="font-size: 6px; padding-top: 4px; word-wrap: break-word; overflow-wrap: break-word; text-wrap: wrap;">
        <a href="<?php echo esc_url($previewLink) ?>" style="text-decoration: none; color: #64748b; font-style: italic; ">
          <?php echo esc_url($previewLink) ?>
        </a>
      </td>
    </tr>
    <?php if (!invoize()->is_paying_or_trial()): ?>
      <tr>
        <td style="text-align: left;font-size: 12px;padding-top: 20px;color:#ddd">
          Invoize by <a href="https://wpsora.com" target="_blank">wpsora.com</a>
        </td>
      </tr>
    <?php endif; ?>
  </table>

</body>

</html>