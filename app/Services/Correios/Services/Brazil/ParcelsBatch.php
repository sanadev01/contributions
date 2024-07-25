<?php

namespace App\Services\Correios\Services\Brazil;

use App\Models\Order;
use App\Models\Warehouse\Container;
use App\Services\Converters\UnitsConverter;
use DateTime;

class ParcelsBatch
{
   protected $container;

   public function __construct(Container $container)
   {
      $this->container = $container;
   }

   public function getBatch()
   {

      if (app()->isProduction()) {
         $webhookUrl = config('correios_customs.production.webhookUrl');
      } else {
         $webhookUrl = config('correios_customs.testing.webhookUrl');
      }

      return [
         "webhookUrl" => $webhookUrl,
         "uaDespacho" => $this->container->dispatch_number,
         "originCountry" => 'US',
         "shippings" => $this->addContainerParcels($this->container)
      ];
   }

   private function addContainerParcels($container)
   {
      $parcels = [];
      if (count($container->orders) >= 1) {
         foreach ($container->orders as $key => $order) {

            $weight = $order->weight;
            if(!$order->isWeightInKg()) {
               $weight = UnitsConverter::poundToKg($order->getOriginalWeight('lbs'));
            }

            $parcels[] = [
               "shipmentNumber" => $order->warehouse_number,
               "trackingCode" => $order->corrios_tracking_code,
               "masterNumber" => ($order->customer_reference ? $order->customer_reference : $order->tracking_id) . ' HD-' . $order->id,
               "totalValue" => $order->getOrderValue(),
               "totalValueCurrency" => $order->getOrderValue(),
               "description" => $order->items->first()->description,
               "weight" => $weight,
               "freight" => $order->user_declared_freight,
               "freightCurrency" => $order->user_declared_freight,
               "volumes" => 1,
               "sender" => [
                  "account" => optional($order)->user->pobox_number,
                  "documentType" => 'CNPJ',
                  "name" => $order->getSenderFullName(),
                  "address" => [
                     "zipCode" => ($order->sender_zipcode) ? $order->sender_zipcode : '33182',
                     "street" => ($order->sender_address) ? $order->sender_address : '2200 NW 129TH AVE',
                     "city" => ($order->sender_city) ? $order->sender_city : 'Miami',
                     "state" => ($order->sender_state_id) ? $order->senderState->code : 'FL',
                     "country" => 'US'
                  ]
               ],
               "customer" => [
                  "documentType" => 'CPF',
                  "document" => ($order->recipient->tax_id) ? $order->recipient->tax_id : '',
                  "name" => $order->recipient->getFullName(),
                  "address" => [
                     "zipCode" => cleanString($order->recipient->zipcode),
                     "street" => $order->recipient->address,
                     "city" => $order->recipient->city,
                     "state" => $order->recipient->State->code,
                     "country" => $order->recipient->country->code
                  ]
               ],
               "items" => $order->items->map(function ($item, $index) {
                  return [
                     "sequence" => (string)($index + 1),
                     "regimeTributacao" => (string)($index + 1),
                     "value" => $item->value,
                     "currency" => $item->value,
                     "unit" => (int)$item->quantity,
                     "quantity" => (string)$item->quantity,
                     "description" => $item->description
                  ];
               })->toArray()
            ];
         }
      }
      return $parcels;
   }

}
