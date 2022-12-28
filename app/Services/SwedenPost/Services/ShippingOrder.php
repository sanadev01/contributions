<?php
namespace App\Services\SwedenPost\Services;

use App\Services\Converters\UnitsConverter;
 
class ShippingOrder { 

   protected $chargableWeight;

   public function getRequestBody($order) {

      $batteryType = ""; 
      $batteryPacking = "";
      if($order->measurement_unit == "lbs/in") { $uom = "LB"; } else { $uom = "KG"; }
      if($order->items()->batteries()->count() || $order->items()->perfumes()->count()) {
         $batteryType = "Lithium Ion Polymer";
         $batteryPacking = "Inside Equipment";
      }
     
     $packet = 
         [ 
            'labelFormat' => "PDF",
            'labelType' => 1,
            'orders' => [
               [
                  //Parcel Information
                  'referenceNo' => ($order->customer_reference) ? $order->customer_reference : '',
                  'trackingNo' => "",
                  'serviceCode' =>"DIRECT.LINK.US.L3",
                  'incoterm' => "DDU",
                  'weight'=> $order->weight,
                  'weightUnit' => $uom,
                  'length' => $order->length,
                  'width' => $order->width,
                  'height' => $order->height,
                  'invoiceValue' => $this->getParcelValue($order),
                  'invoiceCurrency' => "USD",
                  'batteryType' => $batteryType,
                  'batteryPacking' => $batteryPacking,
                  'facility'=> "EWR",
                  //Recipient Information
                  'recipientName' => $order->recipient->getFullName().' '.$order->warehouse_number,
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
                  'shipperPhone' => ($order->sender_phone) ? $order->sender_phone : '',
                  'shipperAddressLine1' => "2200 NW 129TH AVE",
                  'shipperCity' => "Miami",
                  'shipperState' => "FL",
                  'shipperPostcode' => "33182",
                  'shipperCountry' => "US",
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
                    'description' => $item->description,
                    'hsCode' => $item->sh_code,
                    'originCountry' => $originCountryCode ? $originCountryCode: 'US',
                    'itemCount' => (int)$item->quantity,
                    'unitValue' => number_format($item->value),
                    'warehouseNo' => ($order->warehouse_number) ? $order->warehouse_number : '',
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

   private function getParcelValue($order)
   {
      $value = 0;
      foreach ($order->items as $key => $item) {
         $value = number_format($item->value * (int)$item->quantity , 2);
      }
      return $value;
   }
}