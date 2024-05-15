<?php

namespace App\Services\PasarEx\Services;

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
      if (!$this->order->isWeightInKg()) {
         $this->weight = UnitsConverter::poundToKg($this->order->getOriginalWeight('lbs'));
      }
   }

   public function getRequestBody()
   {

      $refNo = $this->order->customer_reference;
      $custotmerReference = ($this->order->customer_reference ? $this->order->customer_reference : $this->order->tracking_id) . ' HD-333' . $this->order->id;

      return [
         "ship_awb" => $custotmerReference,
         "ship_date" => now()->format('Y-m-d'), // You may adjust the date format as needed
         "shipper_name" => $this->order->getSenderFullName(),
         "shipper_contact" => $this->order->sender_phone ?? $this->order->getSenderFullName(), // Assuming contact is the same as the name
         "shipper_email" => $this->order->sender_email,
         "shipper_address" => ($this->order->sender_address) ? $this->order->sender_address : '2200 NW 129TH AVE',
         "shipper_phone" => ($this->order->sender_phone) ? $this->order->sender_phone : '',
         "shipper_postal_code" => ($this->order->sender_zipcode) ? $this->order->sender_zipcode : '33182',
         "shipper_city" => ($this->order->sender_city) ? $this->order->sender_city : 'Miami',
         "shipper_state" => ($this->order->sender_state_id) ? $this->order->senderState->code : 'FL',
         "shipper_country" => $this->order->recipient->country->code,
         "consignee_name" => $this->order->recipient->getFullName(),
         "consignee_contact" => $this->order->recipient->getFullName(), // Assuming contact is the same as the name
         "consignee_email" => $this->order->recipient->email,
         "consignee_address1" => $this->order->recipient->address . ' ' . optional($this->order->recipient)->address2 . ' ' . $this->order->recipient->street_no,
         "consignee_address2" => '', // Adjust as needed
         "consignee_phone" => ($this->order->recipient->phone) ? $this->order->recipient->phone : '',
         "consignee_postal_code" => cleanString($this->order->recipient->zipcode),
         "consignee_city" => $this->order->recipient->city,
         "consignee_state" => $this->order->recipient->State->name,
         "consignee_country" => $this->order->recipient->country->code,
         "description_goods" => "Resmas de papel",
         "pieces" => count($this->order->items),
         "weight_pounds" => $this->order->getWeight('lbs'),
         "weight_kg" => $this->order->getWeight('kg'),
         "unit_of_dimensions" => "cm", // Adjust as needed
         "packages" => [$this->mapPackages()],
         "declared_value_usd" => $this->order->user_declared_freight,

         "tariff_code" => "9807200000",
         "customs_news" => "Debe ser declarado",
         "last_mile_instructions" => 0,
         "last_mile_news" => "Puerta roja",
         "last_mile_cod" => 50000,
         "pasarex_account" => "659523893",
         "reference" => $refNo,
         "consolidado_customer" => $this->order->user_id,
         "consignment_customer" => $this->order->user_id,
         "incoterms"            => strtoupper($this->order->tax_modality),
         "aw_due_date_customer" => now()->format('Y-m-d'),
         "aw_due_time_customer" => now()->format('Y-m-d'),
      ];
   }

   private function mapPackages()
   {
      return [
         'length' => $this->order->length,
         'width'  => $this->order->width,
         'high'   => $this->order->height,
         'weight' => $this->weight
      ];
   }
}
