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
        \Log::info('serviceSubClassCode: '. $serviceSubClassCode);
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
        $this->recipientPhoneNumber = preg_replace('/^\+55/', '', $order->recipient->phone);;
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
    
}