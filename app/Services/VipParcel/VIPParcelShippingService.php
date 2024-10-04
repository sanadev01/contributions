<?php
namespace App\Services\VipParcel;

use App\Models\Order;
use App\Models\ShippingService;


class VIPParcelShippingService
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
        if($shippingService->service_sub_class == ShippingService::VIP_PARCEL_FCP && $this->weight <= 0.453) {
            return true;
        }
        elseif($shippingService->service_sub_class == ShippingService::VIP_PARCEL_PMEI || $shippingService->service_sub_class == ShippingService::VIP_PARCEL_PMI && $this->weight <= $shippingService->max_weight_allowed) {
            return true;
        }
    }


}