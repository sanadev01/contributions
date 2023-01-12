<?php 
namespace App\Services\PostPlus\Services;

use App\Models\Order;

class Parcel{
    protected $order;
public function __construct(Order $order)
{
    $this->order = $order;
}
public function getRequest()
{
    return [ 
            "identifiers"=>[
              "senderParcelNr" => "abc-132"
            ],
            "references"=>[
              "bagNr" => "bagn-132"
            ],
            "parcel"=>[
              "type" => "NotRegistered",
              "parcelGrossWeight" => 0.11,
              "items" => [
                [
                  "description" => "plastic toy",
                  "quantity" => 1,
                  "valuePerItem" => 0.1,
                  "weightPerItem" => 0.1
              ]
              ]
            ],
            "receiver"=>[
              "name" => "Marcio Freite",
              "phone" => "+554345323456",
              "address" => "Praça da República apto 115 13",
              "zipCode" => "01045001",
              "city" => "SP",
              "countryCode" => "BR"
            ],
            "sender"=>[
              "name" => "Naveed",
              "phone" => "+183748575837",
              "email" => "marcio@homediliverybr.com",
              "address" => "2200 NW, 129th Ave  Suite # 10",
              "zipCode" => "33182",
              "city" => "Miami",
              "countryCode" => "US"
            ]
    ];
}
}  
