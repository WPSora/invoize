<?php

namespace Invoize\Models\Invoice\Payment;

use Invoize\Models\Payment as ModelsPayment;
use Invoize\Interfaces\InvoiceContentInterface;
use Invoize\Models\Invoice\Payment\Bank\BankPayment;
use Invoize\Models\Invoice\Payment\Woocommerce\WoocommercePayment;
use Invoize\Models\Invoice\Payment\Paypal\PaypalPayment;
use Invoize\Models\Invoice\Payment\Xendit\XenditPayment;

class Payment implements InvoiceContentInterface
{
    private ?BasePayment $payment;
    private ?PaymentParameter $parameter;

    public static function instance(): self
    {
        return new self;
    }

    public function setParameter(PaymentParameter $param): self
    {
        $this->parameter = $param;
        return $this;
    }

    /** if payment is not bank, must set parameter first before calling this */
    public function setContent(array $payment): self
    {
        if ($payment['method'] == ModelsPayment::PAYPAL) {
            $this->payment = PaypalPayment::instance()
                ->setParameter($this->parameter)->setContent($payment);
        }

        if ($payment['method'] == ModelsPayment::XENDIT) {
            if (isset($payment['isExisting'])) {
                // for migration only/if checkout link already exist
                $this->payment = XenditPayment::instance()->setExisting($payment);
            } else {
                $this->payment = XenditPayment::instance()
                    ->setParameter($this->parameter)->setContent($payment);
            }
        }

        if ($payment['method'] == ModelsPayment::BANK) {
            $this->payment = BankPayment::instance()->setContent($payment);
        }

        if ($payment['method'] == ModelsPayment::WOOCOMMERCE_TRANSACTION) {
            $this->payment = WoocommercePayment::instance()->setContent($payment);
        }

        return $this;
    }

    public function getContent()
    {
        return $this->payment->getContent();
    }
}
