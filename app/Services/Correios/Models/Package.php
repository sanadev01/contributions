<?php


namespace App\Services\Correios\Models;


class Package implements \App\Services\Correios\Contracts\Package
{

    const SERVICE_CLASS_STANDARD = 33162;
    const SERVICE_CLASS_EXPRESS = 33170;
    const SERVICE_CLASS_MINI = 33197;
    const SERVICE_CLASS_SRP = 28;
    const SERVICE_CLASS_SRM = 32;
    const SERVICE_CLASS_PRIORITY = 3440;
    const SERVICE_CLASS_FIRSTCLASS = 3441;
    const SERVICE_CLASS_AJ_Standard = 33164;
    const SERVICE_CLASS_AJ_EXPRESS = 33172;
    const SERVICE_CLASS_MILE_EXPRESS = 33173;
    const SERVICE_CLASS_COLOMBIA_URBANO = 44162;
    const SERVICE_CLASS_COLOMBIA_NACIONAL = 44163;
    const SERVICE_CLASS_COLOMBIA_TRAYETOS = 44164;
    const SERVICE_CLASS_PostNL = 87765;
    const SERVICE_CLASS_GePS = 537;
    const SERVICE_CLASS_GePS_EFormat = 540;
    const SERVICE_CLASS_Prime5 = 773;
    const SERVICE_CLASS_Post_Plus_Registered = 734;
    const SERVICE_CLASS_Post_Plus_EMS = 367;

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
    public $distributionModality = self::SERVICE_CLASS_STANDARD;
    public $taxPaymentMethod = "DDU";
    public $currency = "USD";
    public $freightPaidValue = 0;
    public $insurancePaidValue = 0;
    public $nonNationalizationInstruction = "RETURNTOORIGIN";

    public $items = [];

    public function __toString()
    {
        return json_encode($this);
    }


    public function getDistributionModality(): int
    {
        return self::SERVICE_CLASS_STANDARD;
    }

    public function getService(): int
    {
        return 2;
    }

}
