<?php
namespace App\Services\HoundExpress\Services\CN23;

use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
 
class HoundReceiver{
    private $order;
    public function __construct($order)
    {
       $this->order = $order;  
    }
    function getRequestBody() {
        return [
                "contact"=> [
                    "id"=> 0,
                    "givenName"=> "Destinatario",
                    "surname"=> "Prueba",
                    "surname2"=> ""
                ],
                "apartmentNumber"=> "",
                "city"=> "São Gonçalo",
                "country"=> "BR",
                "state"=> "Rio de Janeiro",
                "email"=> "none@undefined.com",
                "street"=> "Travessa Viana 951",
                "streetNumber"=> "693",
                "zip"=> "26000",
                "status"=> 1,
                "addressType"=> [
                    "id"=> 2
                ],
                "phones"=> [
                    []
                ],
                "phone"=> "(81)83525516"
        ];
    }

}