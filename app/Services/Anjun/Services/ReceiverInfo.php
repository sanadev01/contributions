<?php

namespace App\Services\Anjun\Services;

use App\Models\Order;
use  App\Models\Recipient;
use App\Models\ShippingService;

class ReceiverInfo
{

    public $order;
    public $fullName;
    public $tax_id;               //Recipient tax or cpf // Brazilian personal tax number.
    public $companyName;          //company name
    public $phone;
    public $mobileNumber;
    public $countryCode;          // like：BR
    public $stateCode;
    public $city;                 //city name
    public $zipcode;
    public $email;
    public $address;

    public function __construct(Recipient $recipient, Order $order)
    {
        $this->order = $order;
        $this->fullName        = $recipient->first_name . ' ' . $recipient->last_name;
        $this->tax_id          = $recipient->tax_id;
        $this->companyName     = '';
        $this->phone           = $recipient->phone;
        $this->mobileNumber    = '';
        $this->countryCode     = $recipient->country->code;
        $this->stateCode       = $recipient->state->code;
        $this->city            = $recipient->city;
        $this->zipcode         = $recipient->zipcode;
        $this->email           = $recipient->email;
        $this->address         = $recipient->address;
    }


    public function requestBody()
    {
        $phone = $this->phone;
        if (strlen($phone) > 12 && $this->order->shippingService->service_sub_class == ShippingService::AJ_Express_CN) {
            $phone = substr($this->phone, 0, 3) . substr($this->phone, -9);
        }
        return [
            'receiveName'       => $this->fullName,
            'receivePhone'      => $phone,
            'receiveMobile'     => $phone,
            "receiveMail" => $this->email,
            "receiveCountry" => $this->countryCode,
            "receiveProvince" => $this->stateCode,
            "receiveCity" => $this->city,
            "receiveArea" => "",
            "receiveStreet" => "",
            "receiveHouseNumber" => "",
            "receiveAddress" => $this->address,
            "receiveZipcode" => $this->zipcode,
            "receiveCompany" => $this->companyName,
            "receiveTax" => $this->tax_id,
            "receiveCertificateType" => "",
            "receiveCertificateCode" => ""
        ];
    }
}
