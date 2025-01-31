<?php

namespace App\Services\Anjun\Services;

use App\Models\Order;

class SenderInfo
{

    private $order;
    function __construct( $order)
    {
        $this->order = $order;
    }


    public function requestBody()
    {
        return [ 
            "senderName" => $this->order->sender_first_name .' '. $this->order->sender_last_name,
            "senderPhone" => $this->order->sender_phone??"",
            "senderMobile" => $this->order->sender_phone??"",
            "senderMail" =>$this->order->sender_email,
            "senderCountry" => $this->order->senderCountry->code,
            "senderProvince" => optional($this->order->senderState)->name??"",
            "senderCity" =>  $this->order->sender_city,
            "senderMail" => $this->order->sender_email,
            "senderArea" =>  optional($this->order->senderState)->name??"",
            "senderStreet" => "",
            "senderHouseNumber" => "",
            "senderAddress" => $this->order->sender_address,
            "senderZipcode" => $this->order->sender_zipcode,
            "senderCompany" => "",
            "senderTax" => $this->order->sender_taxId??"",
            "senderCertificateType" => "",
            "senderCertificateCode" => ""
        ];
    }
}
