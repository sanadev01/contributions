<?php

namespace App\Services\Anjun\Services;

use App\Models\Order;
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
        $userZipcode = $user->zipcode??'';
        $userCountryId = $user->country_id;
        $userStateId = $user->state_id;
        $userCity = $user->city;
        $userEmail = $user->email;
        $userAddress= $user->address.' '.$user->address2;
        $userTaxId = $user->tax_id;

        return [
            "senderName" => $this->order->sender_first_name .' '. $this->order->sender_last_name,
            "senderPhone" => $this->order->sender_phone??"",
            "senderMobile" => $this->order->sender_phone??"",
            "senderMail" =>$this->order->sender_email,
            "senderCountry" => optional($this->order->senderCountry)->code??$userCountryId,
            "senderProvince" => optional($this->order->senderState)->name??$userStateId,
            "senderCity" =>  $this->order->sender_city??$userCity,
            "senderMail" => $this->order->sender_email??$userEmail,
            "senderArea" =>  optional($this->order->senderState)->name??$userStateId,
            "senderStreet" => "",
            "senderHouseNumber" => "",
            "senderAddress" => $this->order->sender_address??$userAddress,
            "senderZipcode" => $this->order->sender_zipcode??$userZipcode,
            "senderCompany" => "",
            "senderTax" => $this->order->sender_taxId??$userTaxId,
            "senderCertificateType" => "",
            "senderCertificateCode" => ""
        ];
    }
}
