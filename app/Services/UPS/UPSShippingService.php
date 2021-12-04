<?php
namespace App\Services\UPS;

use App\Models\Order;
use App\Models\ShippingService;


class UPSShippingService
{
    private $order;
    private $weight;
    private $length;
    private $width;
    private $height;
    private $measurement_unit;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->length = $order->length;
        $this->width = $order->width;
        $this->height = $order->height;
        $this->measurement_unit = $order->measurement_unit;

        $this->weight = $order->getWeight('kg');
        // $this->weightCalculator();
    }

    public function isAvailableFor($shippingService)
    {
        if((
            $shippingService->service_sub_class == ShippingService::UPS_FREIGHT_LTL 
            || $shippingService->service_sub_class == ShippingService::UPS_FREIGHT_LTL_GUARANTEED
            || $shippingService->service_sub_class == ShippingService::UPS_FREIGHT_LTL_GUARANTEED_AM
            || $shippingService->service_sub_class == ShippingService::UPS_STANDARD_LTL

            ) 
            && $this->weight <= $shippingService->max_weight_allowed)
        {
            return true;
        }
    }

}