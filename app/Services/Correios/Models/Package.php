<?php


namespace App\Services\Correios\Models;


class Package implements \App\Services\Correios\Contracts\Package
{

    public $customerControlCode = "";
    public $senderName = "HERCO";
    public $senderAddress = "2200 NW, 129th Ave – Suite # 100";
//    public $senderAddressNumber = 201654;
//    public $senderAddressComplement = "comp ";
    public $senderZipCode = "83642555887858953222";
    public $senderCityName = "MIAMI";
//    public $senderState = "";
    public $senderCountryCode = "US";
    public $senderEmail = "homedelivery@homedeliverybr.com";
    public $senderWebsite = "homedeliverybr.com";
    public $recipientName = null;
    public $recipientDocumentType = "CPF";
    public $recipientDocumentNumber = null;
    public $recipientAddress = null;
    public $recipientAddressNumber = null;
    public $recipientAddressComplement = null;
    public $recipientCityName = "BRASILIA";
    public $recipientState = "SP";
    public $recipientZipCode = null;
    public $recipientEmail = null;
//    public $recipientPhoneNumber = null;
    public $totalWeight = 0;
    public $packagingLength = 0;
    public $packagingWidth = 0;
    public $packagingHeight = 0;
    public $distributionModality = 33162;
    public $taxPaymentMethod = "DDU";
    public $currency = "USD";
    public $freightPaidValue = 0;
    public $insurancePaidValue = 0;

    public $items = [];

    public function __toString()
    {
        return json_encode($this);
    }

}
