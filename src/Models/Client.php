<?php

namespace Invoize\Models;

use WP_User_Query;
use Invoize\Classes\Mail;
use Invoize\InvoizePlugin;
use Invoize\Models\Recurring;

class Client extends WPPost
{
    public const CUSTOMER_ROLE = 'invoize_customer';


    public static function postType()
    {
        return "ivz_client";
    }


    // only run once on plugin activation
    public static function addRoleToWordpress()
    {
        add_role(static::CUSTOMER_ROLE, 'Invoize Customer', [
            'read' => true,
            'invoize_customer' => true
        ]);
    }


    public static function wcCustomer(int $page, int $per_page, string $search = '')
    {

        $args = [
            'role'    => 'Customer',
            'orderby' => 'user_registered',
            'order'   => 'DESC',
            'number' => $per_page ?: 10,
            'paged' => $page ?: 1,
        ];

        if (!empty($search)) {
            $args['search'] = '*' . $search . '*';
            $args['search_columns'] = ['user_login', 'user_nicename', 'user_email'];
        }

        $query = new WP_User_Query($args);
        return $query;
    }

    public function scopeWpUserId($query, $userId)
    {
        return $query->whereHas('metas', function ($metas) use ($userId) {
            $metas
                ->where('meta_key', 'wp_user_id')
                ->where('meta_value', $userId);
        });
    }


    public function recurring()
    {
        return $this->hasMany(Recurring::class, 'post_excerpt');
    }


    public function getName()
    {
        return $this->post_title;
    }


    public function setName($name)
    {
        $this->post_title = $name;
    }


    public function createWordpressUser(int $id, string $name, string $email): int
    {
        $plugin = InvoizePlugin::getInstance()->getSlug();
        $pw = substr(md5($name . '-' . $email . '-' . time()), 0, 15);
        $userId = wp_insert_user([
            // saved on user table
            'user_pass' => $pw,
            'user_login' => $email, // username
            'user_nicename' => $email,
            'user_email' => $email,
            // saved on usermeta table
            'nickname' => $name,
            'first_name' => $name,
            'role' => static::CUSTOMER_ROLE,
            'meta_input' => [
                $plugin . '_client_id' => $id,
            ]
        ]);
        $this->sendMail($name, $email, $pw);
        return $userId;
    }


    private function sendMail(string $name, string $email, string $pw)
    {
        $plugin = InvoizePlugin::getInstance()->getPluginName();
        $resetUrl = wp_lostpassword_url();
        $subject = "$plugin Account Credentials";
        $content =
            "Dear <b>{{client}}</b>,
            <br/><br/>We are pleased to inform you that your account has been successfully created automatically by Invoize.
            <br/><br/>Below are your account credentials:
            
            <br/><br/>Email: {{email}}
            <br/>Password: {{password}}

            <br/><br/>You can use this credentials to login to your invoice later on when you receive an invoice.
            <br/><br/>Please change your password at your earliest convenience for security purposes by clicking <a href='{{reset_url}}'><b>here</b>.</a>

            <br/><br/>Thank you.";

        // add bold styling on template content inside {{ }}
        // $content = preg_replace('/\{\{(.*?)\}\}/', '<b>{{$1}}</b>', $content);

        $search_replace = [
            '{{client}}' => "<b>{{client}}</b>",
            '{{email}}' => "<b>{{email}}</b>",
            '{{password}}' => "<b>{{password}}</b>",
        ];

        $content = str_replace(array_keys($search_replace), array_values($search_replace), $content);

        // used inside the template
        $showHeader = false;

        ob_start();
        include(__DIR__ . '/../../templates/email/email-header.php');
        echo wp_kses($content, ['br' => [], 'b' => [], 'strong' => [], 'p' => [], 'a' => ['href' => true]]);
        include(__DIR__ . '/../../templates/email/email-footer.php');
        $emailContent = ob_get_contents();
        ob_end_clean();

        $mail = new Mail();
        $mail->setRecipients([$email]);
        $mail->setSubject($subject);
        $mail->setContent($emailContent, [
            'client' => $name,
            'password' => $pw,
            'email' => $email,
            'reset_url' => $resetUrl,
        ]);
        $mail->send();
    }


    public static function findEmail($email)
    {
        return static::whereHas('metas', function ($metas) use ($email) {
            $metas->where('meta_key', 'email')
                ->where('meta_value', $email);
        })->first();
    }


    public static function createFromWcCustomer(array $customer)
    {
        if ($customer['id'] != 0) {
            $wcCustomer = get_user_by('ID', $customer['id']);
            if (!$wcCustomer) {
                throw new \Exception('Customer not found', 404);
            }
        }

        $clientMetas = [
            'email' => $customer['email'],
            'phoneNumber' => $customer['phoneNumber'],
            'address' => $customer['address'],
            'website' => $customer['website'],
            'zip' => $customer['zip'],
            'preview_access' => 'true', // wc customer always true because already has account
            'wp_user_id' => $customer['id'],
        ];

        // create ivz_client
        $client = static::create(['post_title' => $customer['name']]);
        $client->setMeta($clientMetas);

        // update in User table and add role
        $plugin = InvoizePlugin::getInstance()->getSlug();
        update_user_meta($customer['id'], $plugin . '_client_id', $client->ID);

        if (isset($wcCustomer)) {
            !in_array(Client::CUSTOMER_ROLE, $wcCustomer->roles) && $wcCustomer->add_role(Client::CUSTOMER_ROLE);
        }

        return $client;
    }


    /** change id from wc customer id to ivz_client id, also create ivz_client if not exist yet */
    public static function updateFromWcCustomer(array $wcCustomer): array
    {
        $client = $wcCustomer;
        $wcClientOnInvoize = static::whereHas('metas', function ($metas) use ($wcCustomer) {
            $metas
                ->where('meta_key', 'wp_user_id')
                ->where('meta_value', $wcCustomer['id']);
        })->first();

        if ($wcCustomer['id'] == 0) {
            $wcClientOnInvoize = static::findEmail($wcCustomer['email']);
        }

        // if the woocommerce client already saved as ivz_client, update the id to ivz_client id
        if ($wcClientOnInvoize) {
            $client['id'] = $wcClientOnInvoize->ID;

            // create new ivz_client
        } else {
            $clientModel = static::createFromWcCustomer($wcCustomer);
            $client['id'] = $clientModel->ID;
        }

        return $client;
    }

    public function updateEmail(string $email)
    {
        $this->updateMeta(['email' => $email]);
    }
}
