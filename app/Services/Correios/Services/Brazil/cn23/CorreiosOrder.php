<?php
namespace App\Services\Correios\Services\Brazil\cn23;

use App\Services\Correios\Models\Package;
use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
use App\Services\Correios\Contracts\PacketItem;
class CorreiosOrder extends Package{

    function __construct($order){
        $serviceSubClassCode = $order->getDistributionModality();
        if($order->getDistributionModality() == ShippingService::Packet_Standard || $order->getDistributionModality() == ShippingService::BCN_Packet_Standard){
            $serviceSubClassCode = 33227;
        }
        if($order->getDistributionModality() == ShippingService::BCN_Packet_Express){
            $serviceSubClassCode = ShippingService::Packet_Express; 
        }
        if($order->isWeightInKg()) {
            $weight = UnitsConverter::kgToGrams($order->getOriginalWeight('kg'));
        }else{
            $kg = UnitsConverter::poundToKg($order->getOriginalWeight('lbs'));
            $weight = UnitsConverter::kgToGrams($kg);
        } 
        
        $this->customerControlCode = $order->id;
        $this->senderName = $order->sender_first_name.' '.$order->sender_last_name;
        $this->recipientName = $order->recipient->getFullName();
        $this->recipientDocumentType = $order->recipient->getDocumentType();
        $this->recipientDocumentNumber = cleanString($order->recipient->tax_id);
        $this->recipientAddress = $order->recipient->address;
        $this->recipientAddressComplement = $order->recipient->address2;
        $this->recipientAddressNumber = $order->recipient->street_no;
        $this->recipientZipCode = cleanString($order->recipient->zipcode);
        $this->recipientState = $order->recipient->state->code;
        $this->recipientPhoneNumber = preg_replace('/^\+55/', '', $order->recipient->phone);
        $this->recipientEmail = $order->recipient->email;
        $this->distributionModality = $serviceSubClassCode;
        $this->taxPaymentMethod = $order->getService() == 1 ? 'DDP' : 'DDU';
        $this->totalWeight =  ceil($weight);

        $width = round($order->isMeasurmentUnitCm() ? $order->width : UnitsConverter::inToCm($order->width));
        $height = round($order->isMeasurmentUnitCm() ? $order->height : UnitsConverter::inToCm($order->height));
        $length = round($order->isMeasurmentUnitCm() ? $order->length : UnitsConverter::inToCm($order->length));

        $this->packagingWidth =  $width > 11 ? $width : 11;
        $this->packagingHeight = $height > 2 ? $height : 2;
        $this->packagingLength = $length > 16 ? $length : 16 ;

        $this->freightPaidValue = $order->user_declared_freight;
        $this->nonNationalizationInstruction = "RETURNTOORIGIN";
        
        if(setting('is_prc_user', null, $order->user->id)) {
            $this->senderWebsite = $order->sender_website ? $order->sender_website : 'https://homedeliverybr.com';
            $this->taxPaymentMethod = 'PRC';
            $this->currency = 'USD';
            $this->provisionedTaxValue = $order->calculate_tax_and_duty;
            $this->provisionedtIcmsValue = $order->calculate_icms;
            $this->senderCodeEce = $order->sender_taxId ? $order->sender_taxId : $order->user->tax_id;
            $this->generalDescription = $order->items->first()->description;
        }

        $items = [];

        foreach ($order->items as $item){
            $pItem = new PacketItem();
            $pItem->hsCode = $item->sh_code;
            $pItem->description = $item->description;
            $pItem->quantity = $item->quantity;
            $pItem->value = $item->value;

            $items[] = $pItem;
        }

        $this->items = $items;
    }
    public function __toString()
    {
        return json_encode($this);
    }
    public function getDistributionModality(): int
    {
        return Package::SERVICE_CLASS_STANDARD;
    }
    public function getService(): int
    {
        return 2;
    }
    
    function getRequestBody($order) {

        $serviceSubClassCode = $order->getDistributionModality();
        if($order->getDistributionModality() == ShippingService::Packet_Standard || $order->getDistributionModality() == ShippingService::BCN_Packet_Standard){
            $serviceSubClassCode = 33227;
        }
        if($order->getDistributionModality() == ShippingService::BCN_Packet_Express){
            $serviceSubClassCode = ShippingService::Packet_Express; 
        }
        if($order->isWeightInKg()) {
            $weight = UnitsConverter::kgToGrams($order->getOriginalWeight('kg'));
        }else{
            $kg = UnitsConverter::poundToKg($order->getOriginalWeight('lbs'));
            $weight = UnitsConverter::kgToGrams($kg);
        }

        $width = round($order->isMeasurmentUnitCm() ? $order->width : UnitsConverter::inToCm($order->width));
        $height = round($order->isMeasurmentUnitCm() ? $order->height : UnitsConverter::inToCm($order->height));
        $length = round($order->isMeasurmentUnitCm() ? $order->length : UnitsConverter::inToCm($order->length));
         
            $packet = [
                "customerControlCode"=> $order->id,
                "senderName"=> $order->sender_first_name.' '.$order->sender_last_name,
                "senderAddress"=> "2200 NW, 129th Ave – Suite # 100",
                "senderZipCode"=> "33182",
                "senderCityName"=> "Miami",
                "senderState"=> "FL",
                "senderCountryCode"=> "US",
                "senderEmail"=> "homedelivery@homedeliverybr.com",
                "senderWebsite"=> $order->sender_website ? $order->sender_website : 'www.homedeliverybr.com',
                "recipientName"=> $order->recipient->getFullName(),
                "recipientDocumentType"=> $order->recipient->getDocumentType(),
                "recipientDocumentNumber"=> cleanString($order->recipient->tax_id),
                "recipientAddress"=> $order->recipient->address,
                "recipientAddressNumber"=> $order->recipient->street_no,
                "recipientAddressComplement"=> $order->recipient->address2,
                "recipientCityName"=> $order->recipient->city,
                "recipientState"=> $order->recipient->state->code,
                "recipientZipCode"=> cleanString($order->recipient->zipcode),
                "recipientEmail"=> $order->recipient->email,
                "recipientPhoneNumber"=> preg_replace('/^\+55/', '', $order->recipient->phone),
                "totalWeight"=> ceil($weight),
                "packagingLength"=> $length > 16 ? $length : 16,
                "packagingWidth"=> $width > 11 ? $width : 11,
                "packagingHeight"=> $height > 2 ? $height : 2,
                "distributionModality"=> $serviceSubClassCode,
                "taxPaymentMethod"=> "PRC",
                "currency"=> "USD",
                "nonNationalizationInstruction"=> "RETURNTOORIGIN",
                "freightPaidValue"=> $order->user_declared_freight,
                "insurancePaidValue"=> 0.00,
                "provisionedTaxValue"=> $order->calculate_tax_and_duty,
                "provisionedIcmsValue"=> $order->calculate_icms,
                "senderCodeEce"=> $order->sender_taxId ? $order->sender_taxId : $order->user->tax_id,
                "generalDescription"=> $order->items->first()->description,
                "items"=> $this->getOrderItems($order),
            ];
        return $packet;
    }

    function getOrderItems($order) {

        $items = [];

        if (count($order->items) >= 1) {
            foreach ($order->items as $key => $item) {
               $itemToPush = [];
               $itemToPush = [
                     'hsCode' => $item->sh_code,
                     'description' => $item->description,
                     'quantity' => (int)$item->quantity,
                     'value' => $item->value,
               ];
               $items[] = $itemToPush;
            }
         }
         return $items;
    }
}