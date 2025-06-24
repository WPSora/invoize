<?php

namespace Invoize\Classes;

use Mustache_Engine;
// Di dalam file plugin Anda

/**
 * $mail = new Mail();
 * $mail->setRecipient($recipient);
 * $mail->setContent($content, $vars);
 * $mail->send();
 */


// Buat instance Mustache
class Mail
{
    protected Mustache_Engine $engine;

    protected string $subject;

    protected string $content;

    protected array $recipients = [];

    protected array $vars = [];

    protected array $cc = [];

    protected array $bcc = [];

    protected ?string $attachment = null; // path to attachment

    public function __construct()
    {
        $this->engine = new Mustache_Engine;
    }

    public function setSubject(string $subject, array $vars = [])
    {
        $this->subject = $this->engine->render($subject, $vars);
        return $this;
    }

    public function setRecipients(array $recipients)
    {
        $this->recipients = $recipients;
        return $this;
    }

    public function setContent(string $content, array $vars = [])
    {
        $this->content = $this->engine->render($content, $vars);
        return $this;
    }

    public function setAttachment(string $attachment)
    {
        $this->attachment = $attachment;
        return $this;
    }

    public function setCc(array $cc)
    {
        $this->cc = $cc;
        return $this;
    }

    public function setBcc(array $bcc)
    {
        $this->bcc = $bcc;
        return $this;
    }

    public function getCc()
    {
        return $this->cc;
    }

    public function getBcc()
    {
        return $this->bcc;
    }

    public function getAttachment()
    {
        return $this->attachment;
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function send()
    {
        try {

            if (empty($recipients = $this->getRecipients())) {
                Log::emailError('Recipients is empty');
                throw new \Exception("Recipients is empty");
            }

            if (empty($subject = $this->getSubject())) {
                Log::emailError('Subject is empty');
                throw new \Exception("Subject is empty");
            }

            if (empty($content = $this->getContent())) {
                Log::emailError('Content is empty');
                throw new \Exception("Content is empty");
            }

            $headers = [
                'Content-Type: text/html; charset=UTF-8',
            ];

            if (!empty($cc = $this->getCc())) {
                foreach ($cc as $cc) {
                    $headers[] = 'Cc: ' . $cc;
                }
            }

            if (!empty($bcc = $this->getBcc())) {
                foreach ($bcc as $bcc) {
                    $headers[] = 'Bcc: ' . $bcc;
                }
            }

            if (!function_exists('wp_mail')) {
                Log::emailError('Skipping send email, wp_mail function not found');
            } else {
                $success = false;
                $success = wp_mail(
                    $recipients,
                    $subject,
                    $content,
                    $headers,
                    $this->getAttachment()
                );
                if (!$success) {
                    Log::emailError('Please check your email configuration');
                    throw new \Exception("please check your email configuration");
                }
                Log::email($this->getRecipients(), $this->getSubject());
            }
        } catch (\Exception $e) {
            Log::emailError($e->getMessage());
            throw new \Exception(esc_html("Failed to send email, {$e->getMessage()}"));
        }
    }
}
