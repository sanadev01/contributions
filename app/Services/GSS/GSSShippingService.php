<?php
namespace App\Services\GSS;

use App\Models\Order;
use App\Models\ShippingService;


class GSSShippingService
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
        if(($shippingService->service_sub_class == ShippingService::GSS_PMI || $shippingService->service_sub_class == ShippingService::GSS_EPMEI || $shippingService->service_sub_class == ShippingService::GSS_EPMI || $shippingService->service_sub_class == ShippingService::GSS_FCM || $shippingService->service_sub_class == ShippingService::GSS_EMS || $shippingService->service_sub_class == ShippingService::GSS_CEP) && $this->weight <= $shippingService->max_weight_allowed)
        {
            return true;
        }
    }

    public function isAvailableForInternational($shippingService)
    {
        if(($shippingService->service_sub_class == ShippingService::GSS_PMI || $shippingService->service_sub_class == ShippingService::GSS_EPMEI || $shippingService->service_sub_class == ShippingService::GSS_EPMI || $shippingService->service_sub_class == ShippingService::GSS_FCM || $shippingService->service_sub_class == ShippingService::GSS_EMS || $shippingService->service_sub_class == ShippingService::GSS_CEP) && $this->weight <= $shippingService->max_weight_allowed)
        {
            return true;
        }
    }



}
