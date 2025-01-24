<?php

namespace Invoize\Models\Invoice\Payment;

use Invoize\Interfaces\InvoiceContentInterface;
use Invoize\Models\Invoice\Payment\Payment;

class Payments implements InvoiceContentInterface
{
    protected array $payments = [];
    protected ?PaymentParameter $parameter;

    public static function instance(): self
    {
        return new self;
    }

    public function setParameter(PaymentParameter $param): self
    {
        $this->parameter = $param;
        return $this;
    }

    public function setContent(array $payments): self
    {
        foreach ($payments as $payment) {
            $p = [];
            foreach ($payment as $key => $value) {
                if ($key == 'detail' && is_array($value)) {
                    $p['detail'] = $value;
                    continue;
                }
                $p[$key] = $key == 'detail'
                    ? sanitize_textarea_field($value)
                    : sanitize_text_field($value);
            }
            $this->payments[] = Payment::instance()
                ->setParameter($this->parameter)
                ->setContent($p)
                ->getContent();
        }
        return $this;
    }

    public function getContent(): array
    {
        return $this->payments;
    }

    /** Check error for third party payment (Paypal, Xendit) */
    public function checkError(): self
    {
        foreach ($this->payments as $payment) {
            if (isset($payment['checkout']) && isset($payment['checkout']['error'])) {
                throw new \Exception(esc_html($payment['checkout']['message']), esc_html($payment['checkout']['code']));
            }
        }
        return $this;
    }
}
