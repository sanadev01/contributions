<?php
namespace App\Services\HoundExpress\Services\CN23;

use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
 
class HoundSender{
    private $order;
    public function __construct($order)
    {
       $this->order = $order;  
    }
    function getRequestBody() {
        return [  
                "id"                => $this->order->user_id,
                "contact"           => [
                    "givenName"     =>  $this->order->getSenderFullName(),
                ],
                "city"              => $this->order->sender_city ??"Saginaw",
                "country"           => optional($this->order->senderCountry)->code ,
                "county"            => optional($this->order->senderCountry)->code ?? "MEX",
                "state"             => optional($this->order->senderState)->code ?? "Michigan",
                "email"             => $this->order->sender_email,
                "street"            => $this->order->sender_address ?? "Calle 5",
                "streetNumber"      => "46 y 48",
                "zip"               => $this->order->sender_zipcode ?? 48607,
                "locationReference" => "azul y amarillo",
                "latitud"           => "19.43905509781033",
                "longitud"          => "-99.08403114307254",
                "status"            => 1,
                "company"           => "Hound Express",
                "addressType"       => [
                       "id"         => 1,
                       "code"       => "REM",
                       "type"       => "1",
                       "status"     => 1,
                       "description"=> "REMITENTE"
                ],
                "phone"             => $this->order->sender_phone ??"5634438693",
                "code"              => optional($this->order->senderCountry())->code ?? "MEX",
                "idOrg"             => 1
            ];
    }

}