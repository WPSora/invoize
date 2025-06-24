<?php

namespace Invoize\Models;

use Exception;
use Illuminate\Support\Carbon;
use Invoize\Payments\Paypal\PaypalCheckout;
use Invoize\Payments\Xendit\XenditCheckout;
use Invoize\Payments\Constant\PaymentError;
class Payment extends WPPost {
    public const BANK = 'bank';

    public const PAYPAL = 'paypal';

    public const AUTO_CONFIRMATION = 'auto confirmation';

    public const DIRECT_PAYMENT = 'direct payment';

    public const PAYPAL_DIRECT = 'paypal-direct';

    public const PAYPAL_AUTO_CONFIRMATION = 'paypal-auto-confirmation';

    public const XENDIT = 'xendit';

    public const WOOCOMMERCE = 'woocommerce';

    public const WOOCOMMERCE_TRANSACTION = 'woocommerce transaction';

    public const WOOCOMMERCE_BANK = 'woocommerce bank';

    public const WOOCOMMERCE_PAYPAL = 'woocommerce paypal';

    public const WOOCOMMERCE_BANK_CODE = 'bacs';

    // supported currencies by Xendit + supported converted currencies from Invoize
    public const XENDIT_SUPPORTED_CURRENCIES = [
        'IDR',
        'PHP',
        'THB',
        'VND',
        'MYR',
        'USD',
        'GBP',
        'EUR'
    ];

    public static function postType() {
        return 'ivz_payment';
    }

    private static function createPayment( $checkoutInstance ) {
        return $checkoutInstance->create();
    }

}
