<?php
namespace App\Services\USPS;

use App\Models\Order;
use App\Models\ShippingService;


class USPSShippingService
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
        if($shippingService->service_sub_class == ShippingService::GDE_PRIORITY_MAIL && $this->weight >= 0.454) {
            return true;
        }

        elseif($shippingService->service_sub_class == ShippingService::GDE_FIRST_CLASS && $this->weight <= 0.453) {
            return true;
        }
        elseif($shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS || $shippingService->service_sub_class == ShippingService::USPS_GROUND && $this->weight <= $shippingService->max_weight_allowed) {
            return true;
        }
    }

    public function isAvailableForInternational($shippingService)
    {
        if(($shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL || $shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) && $this->weight <= $shippingService->max_weight_allowed)
        {
            return true;
        }
    }

    public function weightCalculator()
    {
        $volumetric_weight =  $this->getVolumnWeight($this->length, $this->width, $this->height,$this->isWeightInKg($this->measurement_unit));
        $orignal_weight = $this->order->weight;

        if($volumetric_weight > $orignal_weight)
        {
           return $this->weight = $volumetric_weight;
        }

        return $this->weight = $orignal_weight;
    }

    public function isWeightInKg($measurement_unit)
    {
        return $measurement_unit == 'kg/cm' ? 'cm' : 'in';
    }

    public function getVolumnWeight($length, $width, $height, $unit)
    {
        $divisor = $unit == 'in' ? 166 : 6000;
        return round(($length * $width * $height) / $divisor,2);
    }


}