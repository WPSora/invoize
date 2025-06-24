<?php

namespace Invoize\Models;

use Carbon\Carbon;
use Invoize\Classes\Log;
use Invoize\Classes\Mail;
use Invoize\Classes\PDFInvoice;
use Invoize\Classes\Reminder;
use Invoize\InvoizePlugin;
use Invoize\Models\Invoice\ActionHistory;
use Invoize\Models\Invoice\User;

class Quotation extends WPPost
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVE = 'archive';

    public function scopeActiveReminder($query)
    {
        return $query->whereHas('metas', function ($metas) {
            $metas
                ->where('meta_key', 'status')
                ->where('meta_value', static::STATUS_ACTIVE);
        })->where(function ($query) {
            $query->whereHas('metas', function ($metas) {
                $metas
                    ->where('meta_key', 'reminder_before')
                    ->whereNotNull('meta_value');
            })->orWhereHas('metas', function ($metas) {
                $metas
                    ->where('meta_key', 'reminder_after')
                    ->whereNotNull('meta_value');
            });
        });
    }

    public function getPdf()
    {
        $pdf = new PDFInvoice($this);
        return $pdf->generate('quotation');
    }

    public function getQuotationDate()
    {
        return $this->getMeta('quotation_date');
    }

    public function getQuotationNumber()
    {
        return $this->getMeta('quotation_number');
    }

    public function getToken()
    {
        return $this->getMeta('token');
    }

    public function saveActionHistory(string $from, string $to, string $message)
    {
        $history = $this->metas()->where('meta_key', 'action_history')->first();
        $user = User::instance()->setContent(wp_get_current_user())->getContent();
        $actionHistory = ActionHistory::instance()
            ->setUser($user)
            ->setFrom($from)
            ->setTo($to)
            ->setMessage($user['name'] . ' has ' . $message)
            ->getContent();

        if (!$history) {
            $meta = ['action_history' => [$actionHistory]];
            $this->setMeta($this->invoice->ID, $meta);
        } else {
            $arr = unserialize($history->meta_value);
            $arr[] = $actionHistory;
            $history->meta_value = serialize($arr);
            $history->save();
        }
    }

    public function convertToInvoice()
    {
        if ($this->getMeta('status') != static::STATUS_ACTIVE) {
            throw new \Exception('Quotation is not active');
        }

        $invoiceNumber = ((int) invoizeGetOption('invoice.startFromNumber')) + 1;
        $invoiceNumber = invoizeGetOption('invoice.prefix') . $invoiceNumber;

        // remove reminder done key, so the reminder will run again on invoice
        $reminders      = $this->getReminders();
        $reminderBefore = $reminders['before'] ?? [];
        $reminderAfter  = $reminders['after'] ?? [];

        foreach ($reminderBefore as $reminder) {
            unset($reminder['done']);
        }

        foreach ($reminderAfter as $reminder) {
            unset($reminder['done']);
        }


        $invoice = Invoice::create([
            'post_title' => $invoiceNumber,
            'post_content' => $this->getClient()['name'],
        ]);

        $actionHistory = ActionHistory::instance()
            ->setUser($this->getUser())
            ->setTo('active')
            ->setMessage("This invoice has been created from quotation {$this->post_title}")
            ->getContent();


        $invoice->setMeta([
            'token' => invoizeGenerateToken(
                (int) invoizeGetOption('invoice.startFromNumber') + 1,
                $this->getClient()['id']
            ),
            'id'                        => $invoiceNumber,
            'prefix'                    => invoizeGetOption('invoice.prefix'),
            'number'                    => (int) invoizeGetOption('invoice.startFromNumber') + 1,
            'currency'                  => $this->getCurrency(),
            'business'                  => $this->getBusiness(),
            'client'                    => $this->getClient(),
            'billed_to'                 => $this->getBilledTo(),
            'billed_to_same_as_client'  => $this->isBilledToSameAsClient(),
            'invoice_status'            => Invoice::ACTIVE,
            'payment_status'            => Invoice::UNPAID,
            'invoice_date'              => Carbon::now()->format('Y-m-d'),
            'order_date'                => $this->getMeta('quotation_date'),
            'paid_date'                 => NULL,
            'due_date'                  => $this->getDueDate(),
            'products'                  => $this->getProducts(),
            'payments'                  => $this->getPayments(),
            'total'                     => $this->getTotal(),
            'subtotal'                  => $this->getSubtotal(),
            'reminder_before'           => $reminderBefore,
            'reminder_after'            => $reminderAfter,
            'reminder_for_admin'        => $reminders['forAdmin'],
            'reminder_for_client'       => $reminders['forClient'],
            'discount'                  => $this->getDiscounts(),
            'tax'                       => $this->getTaxes(),
            'invoice_note'              => $this->getNotes(),
            'user'                      => $this->getUser(),
            'recurring_id'              => NULL,
            'from_quotation'            => $this->ID,
            'from_quotation_number'     => $this->post_title,
            'is_email_sent'             => false,
            'tab'                       => Invoice::UNPAID,
            'action_history'            => [$actionHistory],
        ]);


        $invoiceDate = Carbon::parse($invoice->getMeta('invoice_date'));

        Setting::recalculateSummary($invoiceDate->year, $invoiceDate->month);

        Reminder::schedule_reminder();

        invoizeUpdateOption(
            'invoice.startFromNumber',
            (int) invoizeGetOption('invoice.startFromNumber') + 1
        );

        $quotationActionHistory = ActionHistory::instance()
            ->setUser($this->getUser())
            ->setTo('active')
            ->setMessage("This quotation has been converted to invoice {$invoice->post_title}")
            ->getContent();

        $this->updateMeta([
            'status' => static::STATUS_ARCHIVE,
            'action_history' => array_merge(
                invoize_mb_unserialize($this->getMeta('action_history')),
                [$quotationActionHistory]
            ),
        ]);

        Log::action('Invoice created from Quotation. Quotation ID: ' . $this->ID . '. Inv ID: ' . $invoice->ID);

        return $invoice->detail();
    }

    public static function sendMail($quotationId, $status, $isFromCron = false)
    {
        $meta       = [];
        $quotation  = static::with('metas')->find($quotationId);

        foreach ($quotation->metas as $m) {
            $meta[$m->meta_key] = is_serialized($m->meta_value)
                ? invoize_mb_unserialize($m->meta_value)
                : $m->meta_value;
        }

        $emails         = $quotation->getClient()['email'] ? [$quotation->getClient()['email']] : [];
        $dueDate        = $quotation->getDueDate(true);
        $quotationDate  = invoizeFormatDate($quotation->getMeta('quotation_date'));
        $invoiceNumber  = $quotation->getMeta('id');
        $clientName     = $quotation->getMeta('client')['name'];
        $token          = $quotation->getMeta('token');

        if ($isFromCron) {
            $emails = [];
            if (($meta['reminder_for_client'] ?? true) && isset($meta['client']['email'])) {
                $emails[] = $meta['client']['email'];
            }
            if ($meta['reminder_for_admin'] ?? false) {
                $emails[] = wp_get_current_user()->user_email;
            }
        }

        if (!$emails) {
            throw new \Exception("Email doesn't exist", 400);
        }

        $templates      = [];
        $templatesTab   = Setting::tab('templates')->get();

        foreach ($templatesTab as $t) {
            $templates[$t->option_name] = is_serialized($t->option_value)
                ? invoize_mb_unserialize($t->option_value)
                : $t->option_value;
        }

        $templateSetting = InvoizePlugin::getInstance()->getSlug() . '.templates';
        $template = NULL;

        switch ($status) {
            case static::STATUS_ACTIVE:
                $template = $templateSetting . '.created_quotation';
                break;
            case 'reminder-before':
                $template = $templateSetting . '.reminder_quotation';
                break;
            default:
                throw new \Exception('Invalid status');
                break;
        }

        // if paid, only send receipt file
        $pdf        = new PDFInvoice($quotation);
        $fileType   = 'quotation';
        $filePath   = $pdf->generateFile($fileType);

        $data = json_decode($templates[$template]);
        $content = str_replace("\n", "<br/>", $data->body);
        // add bold styling on template content inside {{ }}
        $content = preg_replace('/\{\{(.*?)\}\}/', '<b>{{$1}}</b>', $content);

        // used inside the template
        $business = $meta['business'];
        $businessName = $business['business_name'];
        $businessEmail = $business['email'];
        $businessPhone = $business['phone_number'];
        $businessAddress = $business['address'];
        $businessWeb = $business['web'];
        $businessWeb = $businessWeb && !preg_match("/^https?:\/\//", $businessWeb)
            ? "https://" . $businessWeb
            : $businessWeb;
        $businessLogo = $business['logo'];
        $invoiceUrl = esc_url(get_site_url()) . '/invoize-quotation/' . $token;
        $showHeader = true;
        $isReceipt = $status == 'paid';

        ob_start();
        include(__DIR__ . '/../../templates/email/email-header.php');
        echo wp_kses($content, ['br' => [], 'b' => [], 'strong' => [], 'p' => [], 'a' => ['href' => true]]);
        include(__DIR__ . '/../../templates/email/invoice-link.php');
        include(__DIR__ . '/../../templates/email/email-footer.php');
        $emailContent = ob_get_contents();
        ob_end_clean();

        $mail = new Mail();
        $mail->setRecipients($emails);
        $mail->setSubject($data->subject, [
            'business_name' => $businessName,
            'client_name' => $clientName,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
        ]);

        if (property_exists($data, 'cc')) {
            $mail->setCc([$data->cc]);
        }

        if (property_exists($data, 'bcc')) {
            $mail->setBcc([$data->bcc]);
        }

        $mail->setCc([$data->cc]);
        $mail->setBcc([$data->bcc]);
        $mail->setContent($emailContent, [
            'business_name' => $businessName,
            'client_name' => $clientName,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
        ]);
        $mail->setAttachment($filePath);
        $mail->send();

        wp_delete_file($filePath);

        // update is_email_sent meta
        $isSent = $quotation->metas()->where('meta_key', 'is_email_sent')->firstOrNew();
        $isSent->meta_key = 'is_email_sent';
        $isSent->meta_value = true;
        $isSent->save();

        $history = $quotation->metas()->where('meta_key', 'action_history')->first();
        $user =  User::instance()->setContent(wp_get_current_user())->getContent();

        if ($isFromCron) {
            $actionHistory = ActionHistory::instance()
                ->setUser($user)
                ->setMessage('Email sent automatically from schedule')
                ->getContent();
        } else {
            $actionHistory = ActionHistory::instance()
                ->setUser($user)
                ->setMessage($user['name'] . ' has sent email this quotation')
                ->getContent();
        }

        if (!$history) {
            $meta = ['action_history' => [$actionHistory]];
            $invoice->setMeta($invoice->ID, $meta);
        } else {
            $arr = invoize_mb_unserialize($history->meta_value);
            $arr[] = $actionHistory;
            $history->meta_value = serialize($arr);
            $history->save();
        }
    }

    public static function findByToken($token)
    {
        return static::whereHas('metas', function ($query) use ($token) {
            $query->where('meta_key', 'token')->where('meta_value', $token);
        })->first();
    }

    public function getUser()
    {
        return invoize_mb_unserialize($this->getMeta('user'));
    }

    public function getBilledTo()
    {
        return invoize_mb_unserialize($this->getMeta('billed_to', []));
    }

    public function isBilledToSameAsClient()
    {
        return $this->getMeta('billed_to_same_as_client', true) ?? true;
    }

    public function getClient()
    {
        return invoize_mb_unserialize($this->getMeta('client'));
    }

    public function getBusiness()
    {
        return invoize_mb_unserialize($this->getMeta('business'));
    }

    public function getDueDate($usingFormat = false)
    {
        if ($usingFormat) {
            return Carbon::parse($this->getMeta('due_date'))
                ->format(
                    get_option('date_format')
                );
        }
        return $this->getMeta('due_date');
    }

    public function getProducts()
    {
        return invoize_mb_unserialize($this->getMeta('products'));
    }

    public function getCurrency()
    {
        return invoize_mb_unserialize($this->getMeta('currency'));
    }

    public function getTaxes()
    {
        return invoize_mb_unserialize($this->getMeta('tax'));
    }

    public function getDiscounts()
    {
        return invoize_mb_unserialize($this->getMeta('discount'));
    }

    public function getTotal()
    {
        return $this->getMeta('total');
    }

    public function getSubtotal()
    {
        return $this->getMeta('subtotal');
    }

    public function getPayments()
    {
        return invoize_mb_unserialize($this->getMeta('payments'));
    }

    public function getNotes()
    {
        return invoize_mb_unserialize($this->getMeta('quotation_note', ''));
    }

    public function getTerms()
    {
        return $this->getMeta('quotation_note', [
            'note' => '',
            'terms' => '',
            'internalNote' => '',
        ]);
    }

    public function getReminders()
    {
        $before = $this->getMeta('reminder_before', []) ?? [];
        $after = $this->getMeta('reminder_after', []) ?? [];
        $unserializedBefore = invoize_mb_unserialize($before);
        $unserializedAfter = invoize_mb_unserialize($after);

        return [
            'before' =>  $unserializedBefore !== false ? $unserializedBefore : $before,
            'after' => $unserializedAfter !== false ? $unserializedAfter : $after,
            'forClient' => (bool) $this->getMeta('reminder_for_client', true),
            'forAdmin' => (bool) $this->getMeta('reminder_for_admin', false),
        ];
    }

    public function detail()
    {
        $invoice = Invoice::whereHas('metas', function ($query) {
            $query->where('meta_key', 'from_quotation')->where('meta_value', $this->ID);
        })->first();

        if ($invoice) {
            $invoice = $invoice->detail();
        } else {
            $invoice = [];
        }

        return [
            'id'                    => $this->ID,
            'quotationNumber'       => $this->post_title,
            'token'                 => $this->getMeta('token'),
            'quotationDate'         => $this->getMeta('quotation_date'),
            'status'                => $this->getMeta('status'),
            'business'              => $this->getBusiness(),
            'client'                => $this->getClient(),
            'dueDate'               => $this->getDueDate(),
            'billedTo'              => $this->getBilledTo(),
            'billedToSameAsClient'  => $this->isBilledToSameAsClient(),
            'products'              => $this->getProducts(),
            'payments'              => $this->getPayments(),
            'currency'              => $this->getCurrency(),
            'subtotal'              => $this->getSubtotal(),
            'total'                 => $this->getTotal(),
            'discount'              => $this->getDiscounts(),
            'tax'                   => $this->getTaxes(),
            'reminders'             => $this->getReminders(),
            'notes'                 => $this->getNotes(),
            'user'                  => invoize_mb_unserialize($this->getMeta('user')),
            'actionHistory'         => invoize_mb_unserialize($this->getMeta('action_history')),
            'isSent'                => $this->getMeta('is_email_sent', false),
            'invoice'               => $invoice,
        ];
    }

    public static function postType()
    {
        return "ivz_quotation";
    }
}
