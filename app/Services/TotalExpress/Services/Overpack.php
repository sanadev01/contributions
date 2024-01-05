<?php

namespace App\Services\TotalExpress\Services;

use App\Models\Order;
use Carbon\Carbon;
use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter; 
use DateTime;

class Overpack
{

   protected $container; 
   public function __construct($container)
   {
      $this->container = $container;
   }
   public function getRequestBody()
   {
      $orderIds = [];
      $orderNumbers = [];
      $lastMileNumbers = [];
     foreach($this->container->orders as $order){
      $respone = json_decode($order->api_response); 
       
      array_push($orderIds,$respone->orderResponse->data->id);
      array_push($orderNumbers,$respone->orderResponse->data->order_number);
      array_push($lastMileNumbers,$respone->labelResponse->data->cn23_numbers[0]); 
     } 
     return [
            "orders_id"=> "{".implode(',', $orderIds)."}" ,
            "bag_number"=> $this->container->id,
            "order_numbers"=> $orderNumbers,
            "last_mile_numbers"=> $lastMileNumbers
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
