<?php

namespace Invoize\Payments\Constant;

/**
 * Paypal Error:
 * https://developer.paypal.com/api/rest/reference/orders/v2/errors/
 * 
 * Xendit Error:
 * https://developers.xendit.co/api-reference/#create-invoice
 */

class PaymentError
{
    public const INVALID_PAYPAL_KEY = 'INVALID_PAYPAL_KEY';
    public const UNSUPPORTED_CURRENCY = 'UNSUPPORTED_CURRENCY';
    public const XENDIT_UNSUPPORTED_CURRENCY = 'XENDIT_UNSUPPORTED_CURRENCY';
    public const PAYPAL_UNSUPPORTED_CURRENCY = 'PAYPAL_UNSUPPORTED_CURRENCY';
    public const XENDIT_MISSING_PRIMARY_CURRENCY_SETTING = 'XENDIT_MISSING_PRIMARY_CURRENCY_SETTING';
    public const PAYPAL_MISSING_SETTING = 'PAYPAL_MISSING_SETTING';

    // Generic error
    public const PAYPAL_FAILED = 'PAYPAL_FAILED';
    public const XENDIT_FAILED = 'XENDIT_FAILED';
}
