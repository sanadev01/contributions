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
                    "id"=> $this->order->recipient->id,
                    "givenName"=> $this->order->recipient->getFullName(),
                    "surname"=> $this->order->recipient->first_name,
                    "surname2"=> $this->order->recipient->last_name
                ],
                "apartmentNumber"=> "",
                "city"=> optional($this->order->recipient)->city,
                "country"=> $this->order->recipient->country->code,
                "state"=> optional($this->order->recipient->state)->code,
                "email"=>  $this->order->recipient->email ?? '',
                "street"=> optional($this->order->recipient)->address2,
                "streetNumber"=> $this->order->recipient->street_no,
                "zip"=> cleanString($this->order->recipient->zipcode),
                "status"=> 1,
                "addressType"=> [
                    "id"=> 2
                ],
                "phones"=> [
                    [$this->order->recipient->phone]
                ],
                "phone"=> $this->order->recipient->phone ?? ""
        ];
    }

}