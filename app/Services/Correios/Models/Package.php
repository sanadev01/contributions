<?php


namespace App\Services\Correios\Models;

use App\Models\ShippingService;
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
    const SERVICE_CLASS_BCN_Standard = 44164;
    const SERVICE_CLASS_BCN_EXPRESS = 44172;
    const SERVICE_CLASS_GePS = 537;
    const SERVICE_CLASS_GePS_EFormat = 540;
    const SERVICE_CLASS_Prime5 = 773;
    const SERVICE_CLASS_Post_Plus_Registered = 734;
    const SERVICE_CLASS_Post_Plus_EMS = 367;
    const SERVICE_CLASS_Parcel_Post = 541;
    const SERVICE_CLASS_Post_Plus_Prime = 777;
    const SERVICE_CLASS_Post_Plus_Premium = 778;
    const SERVICE_CLASS_Prime5RIO = 357;
    const SERVICE_CLASS_GDE_PRIORITY = 4387;
    const SERVICE_CLASS_GDE_FIRSTCLASS = 4388;
    const SERVICE_CLASS_TOTAL_EXPRESS = ShippingService::TOTAL_EXPRESS;
    const SERVICE_CLASS_HD_Express = 33173;
    const SERVICE_CLASS_LT_PRIME = ShippingService::LT_PRIME;
    const SERVICE_CLASS_Post_Plus_LT_Premium = ShippingService::Post_Plus_LT_Premium;
    const SERVICE_CLASS_Post_Plus_CO_REG = ShippingService::Post_Plus_CO_REG;
    const SERVICE_CLASS_Post_Plus_CO_EMS = ShippingService::Post_Plus_CO_EMS;
    const SERVICE_CLASS_Japan_Prime = ShippingService::Japan_Prime;
    const SERVICE_CLASS_Japan_EMS = ShippingService::Japan_EMS;
    const SERVICE_CLASS_Hound_Express = ShippingService::HoundExpress;
    const SERVICE_CLASS_TOTAL_EXPRESS_10KG = ShippingService::TOTAL_EXPRESS_10KG;
    const SERVICE_CLASS_DSS_SENEGAL = ShippingService::DSS_SENEGAL;
    const SERVICE_CLASS_AJ_Express_CN = ShippingService::AJ_Express_CN;
    const SERVICE_CLASS_AJ_Standard_CN = ShippingService::AJ_Standard_CN;
    const SERVICE_CLASS_MILE_EXPRESS = ShippingService::Mile_Express;

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

    public $provisionedTaxValue = null;
    public $provisionedtIcmsValue = null;
    public $senderCodeEce = null;
    public $generalDescription = null;


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
