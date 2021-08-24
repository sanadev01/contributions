<?php
namespace App\Services\USPS;

use App\Models\Order;


class USPSShippingService
{
    private $order;
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

        $this->weightCalculator();
    }

    public function isAvailableFor($shippingService)
    {
        if($shippingService->service_sub_class == 3440 || $shippingService->service_sub_class == 3441 && $this->weight <= $shippingService->max_weight_allowed)
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