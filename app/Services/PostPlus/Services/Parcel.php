<?php
namespace App\Services\PostPlus\Services;

use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
 
class Parcel { 

   protected $chargableWeight;

   public function getRequestBody($order) {

      if($order->shippingService->service_sub_class == ShippingService::Post_Plus_Registered) {
         $type = 'Registered';
      } elseif($order->shippingService->service_sub_class == ShippingService::Post_Plus_EMS) {
         $type = 'EMS';
      } elseif($order->shippingService->service_sub_class == ShippingService::Post_Plus_Prime || $order->shippingService->service_sub_class == ShippingService::LT_PRIME) {
         $type = 'Prime';
      } elseif($order->shippingService->service_sub_class == ShippingService::Post_Plus_Premium) {
         $type = 'ParcelUPU';
      }  elseif($order->shippingService->service_sub_class == ShippingService::Post_Plus_LT_Premium) {
         $type = 'Premium';
      }
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
                  'type' => $type,
                  'parcelGrossWeight' => $order->weight,
                  'items' => $this->setItemsDetails($order),
               ],
               'additionalInfo' => [
                  'serviceCode' => ($order->shippingService->service_sub_class == ShippingService::LT_PRIME || $order->shippingService->service_sub_class == ShippingService::Post_Plus_LT_Premium) ? "LTPO" : "UZPO",
                  'taxIdentification' => ($order->recipient->tax_id) ? $order->recipient->tax_id: '',
               ],
               //Recipient Information
               'receiver' => [
                  'name' => $order->recipient->getFullName(),
                  'phone' => ($order->recipient->phone) ? $order->recipient->phone: '',
                  'address' => $order->recipient->address.' '.optional($order->recipient)->address2.' '.$order->recipient->street_no,
                  'zipCode' => cleanString($order->recipient->zipcode),
                  'city' => $order->recipient->city,
                  'state' => $order->recipient->State->code,
                  'countryCode' => $order->recipient->country->code,
               ],
               //Sender Information
               'sender' => [
                  'name' => $order->getSenderFullName(),
                  'phone' => ($order->sender_phone) ? $order->sender_phone: '',
                  'email' => ($order->sender_email) ? $order->sender_email: '',
                  'address' => ($order->sender_address) ? $order->sender_address: '2200 NW 129TH AVE',
                  'zipCode' => ($order->sender_zipcode) ? $order->sender_zipcode: '33182',
                  'city' => ($order->sender_city) ? $order->sender_city: 'Miami',
                  'state' => ($order->sender_state_id) ? $order->senderState->code: 'FL',
                  'countryCode' => 'US',
               ],
            ];
      return $packet;
   }

   private function setItemsDetails($order)
   {
        $items = [];
      
        if (count($order->items) >= 1) {
         $totalQuantity = $order->items->sum('quantity');
            foreach ($order->items as $key => $item) {
                $itemToPush = [];
                $originCountryCode = optional($order->senderCountry)->code;
                $itemToPush = [
                     'description' => $item->description,
                     'quantity' => (int)$item->quantity,
                     'hsCode' => $item->sh_code,
                     'valuePerItem' => $item->value,
                     'weightPerItem' => round($order->weight / $totalQuantity, 2) - 0.02,
                ];
               array_push($items, $itemToPush);
            }
        }
        return $items;
   }


}