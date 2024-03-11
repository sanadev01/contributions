<?php

namespace App\Services\TotalExpress\Services;

use App\Models\Order;
use App\Services\Converters\UnitsConverter;
use DateTime;

class Parcel
{

   protected $order;
   protected $weight;
   protected $width;
   protected $height;
   protected $length;

   protected $chargableWeight;
   public function __construct(Order $order)
   {
      $this->order = $order;
      $this->weight = $order->weight;
      if (!$order->is_weight_in_kg) {
         $this->weight = UnitsConverter::poundToKg($order->getOriginalWeight('lbs'));
      }
      $this->width = round($order->isMeasurmentUnitCm() ? $order->width : UnitsConverter::inToCm($order->width));
      $this->height = round($order->isMeasurmentUnitCm() ? $order->height : UnitsConverter::inToCm($order->height));
      $this->length = round($order->isMeasurmentUnitCm() ? $order->length : UnitsConverter::inToCm($order->length));
   }
   public function getRequestBody()
   {

      if (app()->isProduction()) {
         $contractId = config('total_express.production.contractId');
      } else {
         $contractId = config('total_express.test.contractId');
      }
      $streetNo = optional($this->order->recipient)->street_no;
      if ($streetNo == "0") {
         $streetNo = null;
      }
      return [
         "order_number" => $this->order->warehouse_number,
         "contract_id" => $contractId,
         "sales_channel_id" => null,
         // "sales_channel_order_number" => null,
         "incoterm" => "DDP",
         "is_landed_cost" => false,
         "observations" => " ",
         "return_insurance" => false,
         "currency" => "USD",
         "quantity" => 1,
         "estimated_delivery_date" => ((new DateTime())->modify('+3 days'))->format('Y-m-d'),

         'customer_full_name' => $this->order->recipient->getFullName(),
         'customer_document_type' => $this->order->recipient->account_type == "business" ? "CNPJ" : "CPF",
         'customer_address' => $this->order->recipient->address,
         'customer_address_complement' => optional($this->order->recipient)->address2,
         'customer_address_number' => $streetNo,
         'customer_city' => $this->order->recipient->city,
         'customer_state' => $this->order->recipient->State->code,
         'customer_postal_code' => cleanString($this->order->recipient->zipcode),
         'customer_country' => $this->order->recipient->country->code,
         'customer_phone' => ($this->order->recipient->phone) ? substr($this->order->recipient->phone, -11) : '',
         'customer_email' => ($this->order->recipient->email) ? $this->order->recipient->email : '',
         'customer_document_number' => ($this->order->recipient->tax_id) ? $this->order->recipient->tax_id : '',
         "customer_address_reference" => $streetNo,
         "customer_phone_country_code" => substr($this->order->recipient->phone, 0, 3),
         'is_commercial_destination' => $this->order->recipient->account_type == "business" ? true : false,

         'seller_name' => $this->order->getSenderFullName(),
         'seller_address' => ($this->order->sender_address) ? $this->order->sender_address : '2200 NW 129TH AVE',
         // 'addressIsPOBox' => true,
         'seller_city' => ($this->order->sender_city) ? $this->order->sender_city : 'Miami',
         'seller_state' => ($this->order->sender_state_id) ? $this->order->senderState->code : 'FL',
         'seller_zip_code' => ($this->order->sender_zipcode) ? $this->order->sender_zipcode : '33182',
         'seller_country' => 'US',
         'seller_phone' => ($this->order->sender_phone) ? $this->order->sender_phone : $this->order->user->phone,
         'seller_email' => ($this->order->sender_email) ? $this->order->sender_email : $this->order->user->email,
         "seller_tax_number" => "12345678-998A",
         'customerReferenceID' => ($this->order->customer_reference ? $this->order->customer_reference : $this->order->tracking_id) . ' HD-' . $this->order->id,
         'sales_channel_order_number' => ($this->order->customer_reference ? $this->order->customer_reference : $this->order->tracking_id) . ' HD-' . $this->order->id,
         "seller_address_number" => "605",
         "seller_address_complement" => " ",
         "seller_website" => "www.seller.com",

         "volumes_attributes" => [
            [
               "height" => $this->height,
               "length" => $this->length,
               "width" => $this->width,
               "weight" => $this->weight,
               "freight_value" => ((float)$this->order->insurance_value) + ((float)$this->order->user_declared_freight),
               "order_items_attributes" => $this->setItemsDetails()

            ]
         ]
      ];
   }

   private function setItemsDetails()
   {
      $items = [];

      if (count($this->order->items) >= 1) {
         $totalQuantity = $this->order->items->sum('quantity');
         foreach ($this->order->items as $key => $item) {
            $itemToPush = [];
            $itemToPush = [
               "name" => substr($item->description, 20),
               "description" =>  $item->description,
               "value" => $item->value,
               'weight' => round($this->weight / $totalQuantity, 2) - 0.02,
               "hs_code" => substr($item->sh_code, 0, 6),
               "sku" => $item->sh_code,
               "origin_country" => 'US',
               "quantity" => (int)$item->quantity,

            ];
            array_push($items, $itemToPush);
         }
      }
      return $items;
   }
}
