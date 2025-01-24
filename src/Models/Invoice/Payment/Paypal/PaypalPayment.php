<?php

namespace Invoize\Models\Invoice\Payment\Paypal;

use Invoize\Interfaces\ThirdPartyPayment;
use Invoize\Models\Payment;
use Invoize\Payments\Constant\PaymentError;
class PaypalPayment extends ThirdPartyPayment {
    public function __construct() {
        $this->method = Payment::PAYPAL;
    }

    public function setContent( array $paypal ) : self {
        if ( $paypal['type'] == Payment::DIRECT_PAYMENT ) {
            $this->name = Payment::PAYPAL;
            $this->type = Payment::DIRECT_PAYMENT;
            $this->checkout = $paypal['name'];
        }
        return $this;
    }

}
