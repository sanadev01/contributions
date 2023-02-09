<?php
namespace App\Services\PostPlus\Services;

use App\Services\Converters\UnitsConverter;
 
class Parcel { 

   protected $chargableWeight;

   public function getRequestBody($order) {

      $refNo = $order->customer_reference;
      $packet = [
               //Reference Information
               'identifiers' => [
                  'senderParcelNr' => ($refNo ? $refNo : $order->tracking_id).' HD-'.$order->id,
               ],
               'references' => [
                  'bagNr' => $order->warehouse_number
               ],
               //Parcel Information
               'parcel' => [
                  'type' => 'Registered',
                  'parcelGrossWeight' => $order->weight,
                  'items' => $this->setItemsDetails($order),
               ],
               'additionalInfo' => [
                  'serviceCode' => "UZPO",
                  'taxIdentification' => "TAXID",
               ],
               //Recipient Information
               'receiver' => [
                  'name' => $order->recipient->getFullName(),
                  'phone' => ($order->recipient->phone) ? $order->recipient->phone: '',
                  'address' => $order->recipient->address.' '.optional($order->recipient)->address2.' '.$order->recipient->street_no,
                  'zipCode' => cleanString($order->recipient->zipcode),
                  'city' => $order->recipient->city,
                  'countryCode' => $order->recipient->country->code,
               ],
               //Sender Information
               'sender' => [
                  'name' => $order->getSenderFullName(),
                  'phone' => ($order->sender_phone) ? $order->sender_phone: '',
                  'email' => ($order->sender_email) ? $order->sender_email: '',
                  'address' => "2200 NW 129TH AVE",
                  'zipCode' => "33182",
                  'city' => "FL",
                  'countryCode' => "US",
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
                     'quantity' => (int)$item->quantity,
                     'valuePerItem' => $item->value,
                     'weightPerItem' => round($this->calulateItemWeight($order), 2) - 0.05,
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