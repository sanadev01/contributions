<?php
namespace App\Services\HoundExpress\Services\CN23;

use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
use App\Services\HoundExpress\Services\CN23\HoundReceiver;
use App\Services\HoundExpress\Services\CN23\HoundSender;
use App\Services\HoundExpress\Services\CN23\HoundPackagepiece;
class HoundOrder { 
   protected $order = null; 

   public function __construct($order)
   {
      $this->order = $order;   
   }

   public function getRequestBody(){
     return [
         "sender"    => (new HoundSender($this->order))->getRequestBody(),
         "receiver"  => (new HoundReceiver($this->order))->getRequestBody(),
         "currency"  => "USD",
         "clientReference"=> [
            "code"   => "Your reference value/Code"
         ],
         "deliveryOption"=> [
            "id"  => 1
         ],
         "packageType"=> [
            "id"  => 2
         ],
         "insuredValue"=> [
            "code"=> "USD",
            "value"=> "23.4"
         ],
         "packagePieces"=>  (new HoundPackagePiece($this->order))->getRequestBody(),
         "isReturn"=>false
      ];
   }
}