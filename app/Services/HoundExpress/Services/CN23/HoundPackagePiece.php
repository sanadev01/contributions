<?php
namespace App\Services\HoundExpress\Services\CN23;

use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
 
class HoundPackagePiece{
    private $order;
    public function __construct($order)
    {
       $this->order = $order;  
    }
    function getRequestBody() {
        return [
            [
                "height"=> 10,
                "length"=> 10,
                "width"=> 10,
                "weight"=> 1.54,
                "declaredValue"=> 1.0,
                "piece"=> 1,
                "description"=> "SOMEONTENT"
            ]
         ];
    }

}