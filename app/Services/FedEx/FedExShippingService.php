<?php

namespace App\Services\FedEx;

use App\Models\Order;
use App\Models\ShippingService;

class FedExShippingService
{
    public function __construct(Order $order)
    {
        $this->weight = $order->getWeight('kg');
    }

    public function isAvailableFor($shippingService)
    {
        if($shippingService->service_sub_class == ShippingService::FEDEX_GROUND && $this->weight <= $shippingService->max_weight_allowed)
        {
            return true;
        }
    }
}
