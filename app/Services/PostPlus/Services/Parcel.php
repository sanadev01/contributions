<?php

namespace App\Services\PostPlus\Services;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;

class Parcel
{

   protected $chargableWeight;
   protected $order;
   protected $weight;
   public function __construct(Order $order)
   {
      $this->order = $order;
      $this->weight = $order->weight;
      if (!$this->order->is_weight_in_kg) {
         $this->weight = UnitsConverter::poundToKg($this->order->getOriginalWeight('lbs'));
      }
   }

   public function getRequestBody()
   {

      if ($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_Registered || $this->order->shippingService->service_sub_class == ShippingService::Post_Plus_CO_REG) {
         $type = 'Registered';
      } elseif ($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_EMS || $this->order->shippingService->service_sub_class == ShippingService::Post_Plus_CO_EMS) {
         $type = 'EMS';
      } elseif ($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_Prime || $this->order->shippingService->service_sub_class == ShippingService::LT_PRIME) {
         $type = 'Prime';
      } elseif ($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_Premium) {
         $type = 'ParcelUPU';
      } elseif ($this->order->shippingService->service_sub_class == ShippingService::Post_Plus_LT_Premium) {
         $type = 'Premium';
      }
      $refNo = $this->order->customer_reference;

      $packet = [
         //Reference Information
         'identifiers' => [
            'senderParcelNr' => ($refNo ? $refNo : $this->order->tracking_id) . ' HD-' . $this->order->id,
         ],
         'references' => [
            'bagNr' => $this->order->warehouse_number
         ],
         //Parcel Information
         'parcel' => [
            'type' => $type,
            'parcelGrossWeight' => $this->weight,
            'items' => $this->setItemsDetails(),
         ],
         'additionalInfo' => [
            'serviceCode' => ($this->order->shippingService->service_sub_class == ShippingService::LT_PRIME || $this->order->shippingService->service_sub_class == ShippingService::Post_Plus_LT_Premium) ? "LTPO" : "UZPO",
            'taxIdentification' => ($this->order->recipient->tax_id) ? $this->order->recipient->tax_id : '',
         ],
         //Recipient Information
         'receiver' => [
            'name' => $this->order->recipient->getFullName(),
            'phone' => ($this->order->recipient->phone) ? $this->order->recipient->phone : '',
            'address' => $this->order->recipient->address . ' ' . optional($this->order->recipient)->address2 . ' ' . $this->order->recipient->street_no,
            'zipCode' => cleanString($this->order->recipient->zipcode),
            'city' => $this->order->recipient->city,
            'state' => $this->order->recipient->State->code,
            'countryCode' => $this->order->recipient->country->code,
         ],
         //Sender Information
         'sender' => [
            'name' => $this->order->getSenderFullName(),
            'phone' => ($this->order->sender_phone) ? $this->order->sender_phone : '',
            'email' => ($this->order->sender_email) ? $this->order->sender_email : '',
            'address' => ($this->order->sender_address) ? $this->order->sender_address : '2200 NW 129TH AVE',
            'zipCode' => ($this->order->sender_zipcode) ? $this->order->sender_zipcode : '33182',
            'city' => ($this->order->sender_city) ? $this->order->sender_city : 'Miami',
            'state' => ($this->order->sender_state_id) ? $this->order->senderState->code : 'FL',
            'countryCode' => 'US',
         ],
      ];
      return $packet;
   }

   private function setItemsDetails()
   {
      $items = [];

      if (count($this->order->items) >= 1) {
         $totalQuantity = $this->order->items->sum('quantity');
         foreach ($this->order->items as $key => $item) {
            $itemToPush = [];
            $originCountryCode = optional($this->order->senderCountry)->code;
            $itemToPush = [
               'description' => $item->description,
               'quantity' => (int)$item->quantity,
               'hsCode' => $item->sh_code,
               'valuePerItem' => $item->value,
               'weightPerItem' => round($this->weight / $totalQuantity, 2) - 0.02,
            ];
            array_push($items, $itemToPush);
         }
      }
      return $items;
   }
}
