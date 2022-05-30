<?php

namespace App\Services\Colombia;

use App\Models\Order;
use App\Models\ShippingService;

class ColombiaShippingService
{
    public function __construct(Order $order)
    {
        $this->weight = $order->getWeight('kg');
    }

    public function isAvailableFor($shippingService)
    {
        if($shippingService->isColombiaService() && $this->weight <= $shippingService->max_weight_allowed)
        {
            return true;
        }
    }
}
