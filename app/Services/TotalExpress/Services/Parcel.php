<?php

namespace App\Services\TotalExpress\Services;

use App\Models\Order;
use Carbon\Carbon;
use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
use DateTime;

class Parcel
{

   protected $order;
   protected $chargableWeight;
   public function __construct(Order $order)
   {
      $this->order = $order;
   }
   public function getRequestBody()
   {
      if (app()->isProduction()) {
         $contractId = config('total_express.production.contractId');
      } else {
         $contractId = config('total_express.test.contractId');
      }
      return [
         "order_number" => $this->order->id,
         "contract_id" => $contractId,
         "sales_channel_id" => null,
         "sales_channel_order_number" => null,
         "incoterm" => "DDP",
         "is_landed_cost" => false,
         "observations" => " ",
         "return_insurance" => false,
         "currency" => "USD",
         "quantity" => 1,
         "estimated_delivery_date" => ((new DateTime())->modify('+3 days'))->format('Y-m-d'),

         'customer_full_name' => $this->order->recipient->getFullName(),
         'customer_document_type' => "CPF",
         'customer_address' => $this->order->recipient->address,
         'customer_address_complement' => optional($this->order->recipient)->address2,
         'customer_address_number' => optional($this->order->recipient)->stree_no,
         'customer_city' => $this->order->recipient->city,
         'customer_state' => $this->order->recipient->State->code,
         'customer_postal_code' => cleanString($this->order->recipient->zipcode),
         'customer_country' => $this->order->recipient->country->code,
         'customer_phone' => ($this->order->recipient->phone) ? substr($this->order->recipient->phone, -11) : '',
         'customer_email' => ($this->order->recipient->email) ? $this->order->recipient->email : '',
         'customer_document_number' => ($this->order->recipient->tax_id) ? $this->order->recipient->tax_id : '',
         "customer_address_reference" => optional($this->order->recipient)->street_no,
         "customer_phone_country_code" => substr($this->order->recipient->phone, 0, 3),

         'seller_name' => $this->order->getSenderFullName(),
         'seller_address' => ($this->order->sender_address) ? $this->order->sender_address : '2200 NW 129TH AVE',
         // 'addressIsPOBox' => true,
         'seller_city' => ($this->order->sender_city) ? $this->order->sender_city : 'Miami',
         'seller_state' => ($this->order->sender_state_id) ? $this->order->senderState->code : 'FL',
         'seller_zip_code' => ($this->order->sender_zipcode) ? $this->order->sender_zipcode : '33182',
         'seller_country' => 'US',
         'seller_phone' => ($this->order->sender_phone) ? $this->order->sender_phone : '',
         'seller_email' => ($this->order->sender_email) ? $this->order->sender_email : '',
         'seller_tax_number' => '',
         'customerReferenceID' => ($this->order->customer_reference ? $this->order->customer_reference : $this->order->tracking_id) . ' HD-' . $this->order->id,
         "seller_address_number" => "605",
         "seller_address_complement" => "Apartment 99B",
         "seller_website" => "www.seller.com",

         "volumes_attributes" => [
            [
               "height" => $this->order->height,
               "length" => $this->order->length,
               "width" => $this->order->width,
               "weight" => $this->order->weight,
               "freight_value" => $this->order->gross_total,
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
               'weight' => round($this->order->weight / $totalQuantity, 2) - 0.02,
               "hs_code" => $item->sh_code,
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
