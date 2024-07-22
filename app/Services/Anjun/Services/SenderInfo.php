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
        $userZipcode = $user->zipcode??'33182';
        $userAddress= $user->address.' '.$user->address2??"2200 NW, 129th Ave - Suite # 100";
        $userEmail = $user->email; 
        $userPhone = $user->phone??"+13058885191";
        $userCity = $user->city??"Miami";
        $userTaxId = $user->tax_id;
        if($user->country_id && $user->state_id){
            $userCountryId = $user->country_id;
            $userStateId = $user->state_id;
        }
        else{
            $userCountryId= 250;
            $state="FL"; 
            $userStateId=4622;
        }
        $state = State::find($userStateId);
        $senderCountry = Country::find($userCountryId);
        return [
            "senderName" => $this->order->sender_first_name .' '. $this->order->sender_last_name,
            "senderPhone" => $this->order->sender_phone??$userPhone,
            "senderMobile" => $this->order->sender_phone??$userPhone,
            "senderCountry" => optional($this->order->senderCountry)->code??optional($senderCountry)->code,
            "senderProvince" => optional($this->order->senderState)->name??optional($state)->name,
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
