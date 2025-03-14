<?php
namespace App\Services\SwedenPost\Services;

use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
 
class ShippingOrder {

   protected $chargableWeight;
   protected $isDirectlinkCountry = false;
   protected $taxModility = "DDU";
   protected $serviceCode = '';
   protected $order = null;
   protected $batteryType = ""; 
   protected $batteryPacking = "";

   public function __construct($order)
   {
      $this->order = $order; 
      $this->isDirectlinkCountry = $this->order->shippingService->is_directlink_country;
      $this->initTaxModility();
      $this->initServiceCode();
      $this->initFacility();

      if($this->order->hasBattery()){
         $this->batteryType = "Lithium Ion Polymer"; 
         $this->batteryPacking = "Inside Equipment";
      }
   }

   public function getRequestBody(){
     $packet = 
         [
            'labelFormat' => "PDF",
            'labelType' => 1,
            'orders' => [
               [
                  //Parcel Information
                  'referenceNo' => ($this->order->customer_reference ? $this->order->customer_reference : $this->order->tracking_id).' HD-'.$this->order->id,
                  'trackingNo' => "",
                  'serviceCode' => $this->serviceCode,
                  'incoterm' => $this->taxModility,
                  'weight'=> $this->order->weight,
                  'weightUnit' => $this->order->measurement_unit == "lbs/in" ? "LB":"KG",
                  'length' => $this->order->length,
                  'width' => $this->order->width,
                  'height' => $this->order->height,
                  'invoiceValue' => $this->order->getOrderValue(),
                  'invoiceCurrency' => "USD",
                  'batteryType' => $this->batteryType,
                  'batteryPacking' => $this->batteryPacking,
                  'facility'=> $this->facility,
                  //Recipient Information
                  'recipientName' => $this->order->recipient->getFullName(),
                  'phone' => $this->order->recipient->phone ?? '',
                  'email' => $this->order->recipient->email ?? '',
                  'addressLine1' => $this->order->recipient->address.', '. ($this->order->recipient->street_no == 0 ? ' ' : $this->order->recipient->street_no),
                  'addressLine2' => optional($this->order->recipient)->address2,
                  'city' => optional($this->order->recipient)->city,
                  'state' => optional($this->order->recipient->state)->code,
                  'postcode' => cleanString($this->order->recipient->zipcode),
                  'country' => $this->order->recipient->country->code,
                  'recipientTaxId'=>optional($this->order->recipient)->tax_id,
                  //Shipper Information
                  'shipperName' => $this->order->getSenderFullName(),
                  'shipperPhone' => $this->order->sender_phone ?? '+13058885191',
                  'shipperAddressLine1' => $this->order->sender_address ?? "2200 NW 129TH AVE",
                  'shipperCity' => $this->order->sender_city ?? "Miami",
                  'shipperState' => optional($this->order->senderState())->code ?? "FL",
                  'shipperPostcode' => $this->order->sender_zipcode ?? "33182",
                  'shipperCountry' => optional($this->order->senderCountry())->code ?? "US",
                  //Parcel Return Information
                  "returnOption" =>"",
                  "returnName" => $this->order->getSenderFullName(),
                  "returnAddressLine1" =>"2200 NW 129TH AVE",
                  "returnAddressLine2" =>"",
                  "returnAddressLine3" =>"",
                  "returnCity" =>"Miami",
                  "returnState" =>"FL",
                  "returnPostcode" =>"33182",
                  "returnCountry" =>"US",
                  //Parcel Items Information
                  'orderItems' => $this->setItemsDetails(),
                  'packinglist' => true
               ],
            ],
         ];
         if($this->isDirectlinkCountry){
            $packet['extendData'] = [
               "originPort"=> "JFK",
               "vendorid"=> ""
            ];
         }  
      
      return $packet;
   }

   private function setItemsDetails()
   {
        $items = [];
        $singleItemWeight = UnitsConverter::kgToGrams($this->calulateItemWeight());
        
        if (count($this->order->items) >= 1) {
            foreach ($this->order->items as $key => $item) {
                $itemToPush = [];
                $originCountryCode = optional($this->order->senderCountry)->code;
                $itemToPush = [
                    'hsCode' => $item->sh_code,
                    'originCountry' => $originCountryCode ?? 'US',
                    'description' => $item->description,
                    'unitValue' => $item->value,
                    'itemCount' => (int)$item->quantity,
                ];
                if($this->isDirectlinkCountry){
                  $itemToPush['weight'] = round($this->calulateItemWeight(), 2) - 0.05;
                  $itemToPush['sku'] = $item->sh_code.'-'.$this->order->id;
                }
               array_push($items, $itemToPush);
            }
        }
        return $items;
   }

   function initTaxModility() {
         
      $this->taxModility = "DDU";
      if($this->order->recipient->country->code == 'MX'||$this->order->recipient->country->code == 'AU'||$this->order->recipient->country->code == 'CA'||$this->order->recipient->country->code == 'CO')
      $this->taxModility = strtoupper($this->order->tax_modality)??"DDU";
   }

   function initServiceCode() {
         // service code
         if($this->order->shippingService->service_sub_class == ShippingService::Prime5) {
            $this->serviceCode = 'DIRECT.LINK.US.L3';
         }elseif($this->order->shippingService->isSwedenPostService()) {
            if($this->isDirectlinkCountry){
               if($this->taxModility == "DDP"){
                  $this->serviceCode = 'DLUS.DDP.NJ03';
                  if($this->order->recipient->country->code == 'MX')
                  $this->serviceCode = 'DLUS.TX.DDP';
               }
               else{

                  $this->serviceCode = 'DIRECT.LINK.ST.CONS.NJ';
               }
            }
            else{
               $this->serviceCode = 'DIRECT.LINK.US.L3P';
            }
         }
   }

   function initFacility(){
      // DDU Chile ex-EWR-Newark
      // DDU Australia,Canada, Colombia via EWR and Mexico DDP via LRD-Laredo
      $this->facility = 'EWR';
      if($this->order->recipient->country->code == 'MX' && $this->taxModility == "DDP"){
         $this->facility = "LRD";
      }
   }

   
   private function calulateItemWeight()
   {
        $this->orderTotalWeight = ($this->chargableWeight != null) ? (float)$this->chargableWeight : (float)$this->order->weight;
        $itemWeight = 0;
        if (count($this->order->items) > 1) {
            $itemWeight = $this->orderTotalWeight / count($this->order->items);
            return $itemWeight;
        }
        return $this->orderTotalWeight;
   }
}