<?php

namespace App\Services\HoundExpress\Services\CN23;
use App\Services\HoundExpress\Services\CN23\HoundReceiver;
use App\Services\HoundExpress\Services\CN23\HoundSender;
use App\Services\HoundExpress\Services\CN23\HoundPackagePiece;

class HoundOrder
{
   protected $order = null;

   public function __construct($order)
   {
      $this->order = $order;
   }

   public function getRequestBody()
   {
      return [
         "sender"    => (new HoundSender($this->order))->getRequestBody(),
         "receiver"  => (new HoundReceiver($this->order))->getRequestBody(),
         "currency"  => "USD",
         "clientReference" => [
            "code"   => "Your reference value/Code"
         ],
         "deliveryOption" => [
            "id"  => 1
         ],
         "packageType" => [
            "id"  => 2
         ],
         "insuredValue" => [
            "code" => "USD",
            "value" => $this->order->insurance_value
         ],
         "packagePieces" => (new HoundPackagePiece($this->order))->getRequestBody(),
         "isReturn" => false
      ];
   }
}
