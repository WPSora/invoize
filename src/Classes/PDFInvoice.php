<?php

namespace Invoize\Classes;

use Dompdf\Dompdf;
use Dompdf\Options;
use Invoize\Models\Invoice as InvoiceModel;
use Invoize\Models\Setting;
use Invoize\Models\WPPost;
use Pelago\Emogrifier\CssInliner;

class PDFInvoice
{
    protected WPPost $record;
    public const PAPER_SIZE_CUSTOM_1 = [0, 0, 162, 1008]; // 2 1/4 inches
    public const PAPER_SIZE_CUSTOM_2 = [0, 0, 225, 1008]; // 3 1/8 inches
    public const SUPPORTED_PAPER_SIZE = ["a4", "folio", "legal", "letter"];


    public function getTemplatePath(bool $isSmallPaper)
    {
        if ($isSmallPaper) {
            return __DIR__ . '/../../templates/email/invoice/invoice-template-small.php';
        }
        return __DIR__ . '/../../templates/email/invoice/invoice-template.php';
    }


    public function __construct(WPPost $record)
    {
        $this->record = $record;
    }


    public function getHtml(string $page, bool $isSmallPaper = false)
    {
        $record = $this->record;
        ob_start();

        include(
            $this->getTemplatePath($isSmallPaper)
        );

        $html = ob_get_contents();
        $html = CssInliner::fromHtml($html)->inlineCss()->render();
        ob_end_clean();

        return $html;
    }

    public function generate(string $type)
    {
        if (!in_array($type, [InvoiceModel::INVOICE, InvoiceModel::RECEIPT, 'quotation'])) {
            throw new \Exception('Invalid type');
        }

        $paperSize          = Setting::key("invoice.downloadPaperSize")->value("option_value");
        $isSmallPaper       = $paperSize === "custom-1" || $paperSize === "custom-2";
        $paper              = $this->getPaperSize($paperSize);
        $options            = new Options();
        $options->set('isRemoteEnabled', true);

        $engine = new Dompdf($options);
        $html   = $this->getHtml($type, $isSmallPaper);
        $html   = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $engine->loadHtml($html);
        $engine->setPaper($paper, 'portrait');
        $engine->render();

        return $engine->output();
    }

    public function generateFile(string $type): string
    {
        global $wp_filesystem;

        if ($type != InvoiceModel::INVOICE && $type != InvoiceModel::RECEIPT && $type != 'quotation') {
            throw new \Exception('Invalid type');
        }

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $engine = new Dompdf($options);
        $html   = $this->getHtml($type);

        $engine->loadHtml($html);
        $engine->setPaper('A4', 'portrait');
        $engine->render();

        $output = $engine->output();

        $tempDir    = get_temp_dir();
        $inv        = $this->record;
        $number     = $type == 'quotation' ? $inv->getQuotationNumber() : $inv->getInvoiceNumber();
        $filename   = "{$number} - {$inv->getClient()['name']}.pdf";

        $wp_filesystem->put_contents($tempDir . $filename, $output);
        return $tempDir . $filename;
    }

    private function getPaperSize($name)
    {
        if ($name === 'custom-1') {
            return static::PAPER_SIZE_CUSTOM_1;
        }
        if ($name === 'custom-2') {
            return static::PAPER_SIZE_CUSTOM_2;
        }
        if (in_array($name, static::SUPPORTED_PAPER_SIZE)) {
            return $name;
        }
        throw new \Exception('Invalid paper size');
    }
}
