<?php

namespace Invoize\Models\Invoice;

use Invoize\Interfaces\InvoiceContentInterface;
use Invoize\Models\Business as ModelsBusiness;

class Business implements InvoiceContentInterface
{
    public $id;
    public $business_name;
    public $phone_number;
    public $email;
    public $web;
    public $address;
    public $zip;
    public $logo; // url


    public static function instance(): self
    {
        return new self;
    }


    public function setContentByDefault(): self
    {
        $business = ModelsBusiness::getDefault();
        if (!$business) {
            throw new \Exception('Business default not found', 404);
        }

        $this->id = $business->ID;
        $this->business_name = $business->post_title;

        $phone = $business->metas()->where('meta_key', 'phone_number')->first();
        if ($phone && !empty($phone->meta_value)) {
            $this->phone_number = $phone->meta_value;
        }

        $email = $business->metas()->where('meta_key', 'email')->first();
        if ($email && !empty($email->meta_value)) {
            $this->email = $email->meta_value;
        }

        $web = $business->metas()->where('meta_key', 'web')->first();
        if ($web && !empty($web->meta_value)) {
            $this->web = $web->meta_value;
        }

        $address = $business->metas()->where('meta_key', 'address')->first();
        if ($address && !empty($address->meta_value)) {
            $this->address = $address->meta_value;
        }

        $zip = $business->metas()->where('meta_key', 'zip')->first();
        if ($zip && !empty($zip->meta_value)) {
            $this->zip = $zip->meta_value;
        }

        $logo = $business->metas()->where('meta_key', 'logo')->first();
        if ($logo && !empty($logo->meta_value)) {
            $logoPost = get_post($logo->meta_value);
            if ($logoPost) {
                $this->logo = $logoPost->guid;
            }
        }

        return $this;
    }


    private function required(): array
    {
        return ['id', 'business_name', 'phone_number', 'email'];
    }


    public function setContent(array $business): self
    {
        foreach ($this->required() as $field) {
            if (!$business[$field]) {
                throw new \Exception(esc_html('Missing business field: ' . $field));
            }
        }
        $this->id = isset($business['id'])
            ? sanitize_text_field($business['id'])
            : null;
        $this->business_name = isset($business['business_name'])
            ? sanitize_text_field($business['business_name'])
            : null;
        $this->phone_number = isset($business['phone_number'])
            ? sanitize_text_field($business['phone_number'])
            : null;
        $this->email = isset($business['email'])
            ? sanitize_email($business['email'])
            : null;
        $this->web = isset($business['web'])
            ? esc_url($business['web'])
            : null;
        $this->address = isset($business['address'])
            ? sanitize_textarea_field($business['address'])
            : null;
        $this->zip = isset($business['zip'])
            ? sanitize_text_field($business['zip'])
            : null;
        $this->logo = isset($business['logo'])
            ? sanitize_text_field($business['logo'])
            : null;
        return $this;
    }


    public function getContent(): array
    {
        return [
            'id' => $this->id,
            'business_name' => $this->business_name,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'web' => $this->web,
            'address' => $this->address,
            'zip' => $this->zip,
            'logo' => $this->logo,
        ];
    }
}
