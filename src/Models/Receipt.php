<?php

namespace Invoize\Models;

use Invoize\Classes\PDFInvoice;

class Receipt extends WPPost
{
    public static function postType()
    {
        return "ivz_receipt";
    }

    public function getReceiptNumber()
    {
        return $this->post_title;
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'post_content');
    }

    public function getPdf()
    {
        $pdf = new PDFInvoice($this->invoice);
        return $pdf->generate(Invoice::RECEIPT);
    }
}
