<?php

namespace Invoize\Models\Invoice;

use Invoize\Interfaces\InvoiceContentInterface;
use WP_User;

class User implements InvoiceContentInterface
{
    private string $name = '';
    private string $email = '';
    private string $username = '';

    public static function instance()
    {
        return new self;
    }

    public function setContent(WP_User $user): self
    {
        $this->name = sanitize_text_field($user->display_name);
        $this->email = sanitize_email($user->user_email);
        $this->username = sanitize_text_field($user->user_login);
        return $this;
    }

    public function getContent()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username
        ];
    }
}
