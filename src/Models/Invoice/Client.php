<?php

namespace Invoize\Models\Invoice;

use Invoize\Interfaces\InvoiceContentInterface;
use Invoize\Models\Client as ModelsClient;

class Client implements InvoiceContentInterface
{
    public $id;
    public $name;
    public $email;
    public $phone_number;
    public $address;
    public $zip;
    public $website;
    public $created_at;
    public $customAddress;
    public $is_wc_client;

    public static function instance(): self
    {
        return new self;
    }

    private function required(): array
    {
        // return ['id', 'name'];
        // return ['name', 'email'];
        // E-Mail doesn't required, maybe the user just want to generate a client without email
        // because they want to send the PDF manually
        return ['name'];
    }

    public function setContent(array $client): self
    {
        foreach ($this->required() as $field) {
            if (!$client[$field]) {
                throw new \Exception(esc_html('Missing customer field: ' . $field));
            }
        }

        $ivzClient  = ModelsClient::find($client['id']);
        // if not exist mean the id is wc customer id.
        if (!$ivzClient) {
            $client = ModelsClient::updateFromWcCustomer($client);
        }

        $this->id = isset($client['id'])
            ? sanitize_text_field($client['id']) : null;
        $this->name = sanitize_text_field($client['name']);
        $this->email = sanitize_email($client['email']);
        $this->phone_number = isset($client['phoneNumber'])
            ? sanitize_text_field($client['phoneNumber']) : null;
        $this->address = isset($client['address'])
            ? sanitize_textarea_field($client['address']) : null;
        $this->zip = isset($client['zip']) ? sanitize_text_field($client['zip']) : null;
        $this->website = isset($client['website']) ? esc_url($client['website']) : null;
        $this->created_at = isset($client['created_at'])
            ? sanitize_text_field($client['created_at']) : null;
        $this->customAddress = isset($client['customAddress']) && !empty($client['customAddress'])
            ? sanitize_textarea_field($client['customAddress']) : null;
        $this->is_wc_client = isset($client['isWcClient']) && $client['isWcClient']
            ? 'true' : 'false';
        return $this;
    }

    public function getContent(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phoneNumber' => $this->phone_number,
            'address' => $this->address,
            'zip' => $this->zip,
            'website' => $this->website,
            'created_at' => $this->created_at,
            'customAddress' => $this->customAddress,
            'isWcClient' => $this->is_wc_client,
        ];
    }
}
