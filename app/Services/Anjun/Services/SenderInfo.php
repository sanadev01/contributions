<?php

namespace App\Services\Anjun\Services;

use App\Models\Country;
use App\Models\Order;
use App\Models\State;
use Illuminate\Support\Facades\Auth;

class SenderInfo
{

    private $order;
    function __construct( $order)
    {
        $this->order = $order;
    }


    public function requestBody()
    {
        $user = Auth::user(); 
        $userAddress=  "2200 NW, 129th Ave - Suite # 100"; 
        $userPhone = $user->phone??"+13058885191"; 
        $userTaxId = $user->tax_id;  
        return [
            "senderName" => $this->order->sender_first_name .' '. $this->order->sender_last_name,
            "senderPhone" => $this->order->sender_phone??$userPhone,
            "senderMobile" => $this->order->sender_phone??$userPhone,
            "senderCountry" =>"US",
            "senderProvince" =>"Florida",
            "senderCity" =>  "Miami",
            "senderMail" => $this->order->sender_email??$user->email,
            "senderArea" => "Florida",
            "senderStreet" => "",
            "senderHouseNumber" => "",
            "senderAddress" => $this->order->sender_address??$userAddress,
            "senderZipcode" => $this->order->sender_zipcode??"33182",
            "senderCompany" => "",
            "senderTax" => $this->order->sender_taxId??$userTaxId,
            "senderCertificateType" => "",
            "senderCertificateCode" => ""
        ];
    }
}
