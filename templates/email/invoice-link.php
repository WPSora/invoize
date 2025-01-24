<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly
?>
<div class="invoice-link-button-wrapper" style="margin: 52px 0;">
    You can view the <?php echo $isReceipt ?  'receipt' : 'invoice' ?> on the web by clicking
    <a href="<?php echo esc_url($invoiceUrl) ?>" style="font-weight: 800;">
        here
    </a>
    or see on the attachment file below.
</div>