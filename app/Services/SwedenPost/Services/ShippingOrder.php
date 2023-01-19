<?php
namespace App\Services\SwedenPost\Services;

use App\Services\Converters\UnitsConverter;
 
class ShippingOrder { 

   protected $chargableWeight;

   public function getRequestBody($order) {
      
      $batteryType = ""; 
      $batteryPacking = "";
      $refNo = $order->customer_reference;
      if($order->measurement_unit == "lbs/in") { $uom = "LB"; } else { $uom = "KG"; }
      if($order->hasBattery()) {
         $batteryType = "Lithium Ion Polymer"; $batteryPacking = "Inside Equipment";
      }
     
     $packet = 
         [ 
            'labelFormat' => "PDF",
            'labelType' => 1,
            'orders' => [
               [
                  //Parcel Information
                  'referenceNo' => $refNo ? $refNo : $order->tracking_id,
                  'trackingNo' => "",
                  'serviceCode' =>"DIRECT.LINK.US.L3",
                  'incoterm' => "DDU",
                  'weight'=> $order->weight,
                  'weightUnit' => $uom,
                  'length' => $order->length,
                  'width' => $order->width,
                  'height' => $order->height,
                  'invoiceValue' => $order->getOrderValue(),
                  'invoiceCurrency' => "USD",
                  'batteryType' => $batteryType,
                  'batteryPacking' => $batteryPacking,
                  'facility'=> "EWR",
                  //Recipient Information
                  'recipientName' => $order->recipient->getFullName(),
                  'phone' => ($order->recipient->phone) ? $order->recipient->phone: '',
                  'email' => ($order->recipient->email) ? $order->recipient->email: '',
                  'addressLine1' => $order->recipient->address.' '.$order->recipient->street_no,
                  'addressLine2' => optional($order->recipient)->address2,
                  'city' => $order->recipient->city,
                  'state' => $order->recipient->state->code,
                  'postcode' => cleanString($order->recipient->zipcode),
                  'country' => $order->recipient->country->code,
                  //Shipper Information
                  'shipperName' => $order->getSenderFullName(),
                  'shipperPhone' => ($order->sender_phone) ? $order->sender_phone : '+13058885191',
                  'shipperAddressLine1' => ($order->sender_address) ? $order->sender_address : "2200 NW 129TH AVE",
                  'shipperCity' => ($order->sender_city) ? $order->sender_city : "Miami",
                  'shipperState' => (optional($order->senderState())->code) ? optional($order->senderState())->code : "FL",
                  'shipperPostcode' => ($order->sender_zipcode) ? $order->sender_zipcode : "33182",
                  'shipperCountry' => (optional($order->senderCountry())->code) ? optional($order->senderCountry())->code : "US",
                  //Parcel Return Information
                  "returnOption" =>"",
                  "returnName" => $order->getSenderFullName(),
                  "returnAddressLine1" =>"2200 NW 129TH AVE",
                  "returnAddressLine2" =>"",
                  "returnAddressLine3" =>"",
                  "returnCity" =>"Miami",
                  "returnState" =>"FL",
                  "returnPostcode" =>"33182",
                  "returnCountry" =>"US",
                  //Parcel Items Information
                  'orderItems' => $this->setItemsDetails($order)
               ],
            ],
         ];
      return $packet;
   }

   private function setItemsDetails($order)
   {
        $items = [];
        $singleItemWeight = UnitsConverter::kgToGrams($this->calulateItemWeight($order));
        
        if (count($order->items) >= 1) {
            foreach ($order->items as $key => $item) {
                $itemToPush = [];
                $originCountryCode = optional($order->senderCountry)->code;
                $itemToPush = [
                    'hsCode' => $item->sh_code,
                    'originCountry' => $originCountryCode ? $originCountryCode: 'US',
                    'description' => $item->description,
                  //   'weight' => round($this->calulateItemWeight($order), 2) - 0.05,
                    'itemNo' => "000".++$key,
                  //   'sku' => $item->sh_code.'-'.$order->id,
                    'unitValue' => $item->value,
                    'itemCount' => (int)$item->quantity,
                ];
               array_push($items, $itemToPush);
            }
        }

        return $items;
   }

   private function calulateItemWeight($order)
   {
        $orderTotalWeight = ($this->chargableWeight != null) ? (float)$this->chargableWeight : (float)$order->weight;
        $itemWeight = 0;
        if (count($order->items) > 1) {
            $itemWeight = $orderTotalWeight / count($order->items);
            return $itemWeight;
        }
        return $orderTotalWeight;
   }

}