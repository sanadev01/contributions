<?php
namespace App\Services\GePS;

use App\Models\Order;
use App\Models\ShippingService;


class GePSShippingService
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
    }

    public function isAvailableFor($shippingService)
    {
        if(($shippingService->service_sub_class == ShippingService::GePS || $shippingService->service_sub_class == ShippingService::GePS_EFormat || $shippingService->service_sub_class == ShippingService::Parcel_Post) && $this->weight <= $shippingService->max_weight_allowed)
        {
            return true;
        }
    }

    public function isAvailableForInternational($shippingService)
    {
        if(($shippingService->service_sub_class == ShippingService::GePS || $shippingService->service_sub_class == ShippingService::GePS_EFormat || $shippingService->service_sub_class == ShippingService::Parcel_Post) && $this->weight <= $shippingService->max_weight_allowed)
        {
            return true;
        }
    }



}
