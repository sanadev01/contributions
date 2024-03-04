<?php

namespace App\Services\HoundExpress\Services\CN23;

use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;

class HoundPackagePiece
{
    private $order;
    public function __construct($order)
    {
        $this->order = $order;
    }
    function getRequestBody()
    {
        return [
            [
                "height" => $this->order->height,
                "length" =>  $this->order->length,
                "width" =>  $this->order->width,
                "weight" =>  $this->order->weight,
                "declaredValue" => $this->order->gross_total,
                "piece" => count($this->order->items),
                "description" => optional(optional($this->order->items)[0])->description
            ]
        ];
    }
}
