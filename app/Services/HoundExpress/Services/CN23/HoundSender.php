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
                "id"=> 1,
                "contact"=> [
                    "givenName"=> "Herco Inc."
                ],
                "city"=> "Saginaw",
                "country"=> "US",
                "county"=> "Agricola Pantitlan",
                "state"=> "Michigan",
                "email"=> "jr2it@hound-express.com",
                "street"=> "Calle 5",
                "streetNumber"=> "46 y 48",
                "zip"=> 48607,
                "locationReference"=> "azul y amarillo",
                "latitud"=> "19.43905509781033",
                "longitud"=> "-99.08403114307254",
                "status"=> 1,
                "company"=> "Hound Express",
                "addressType"=> [
                    "id"=> 1,
                    "code"=> "REM",
                    "type"=> "1",
                    "status"=> 1,
                    "description"=> "REMITENTE"
                ],
                "phone"=> "5634438693",
                "code"=> "MEX",
                "idOrg"=> 1
            ];
    }

}