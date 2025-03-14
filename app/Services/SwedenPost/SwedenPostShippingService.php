<?php
namespace App\Services\SwedenPost;

use App\Models\Order;
use App\Models\ShippingService;


class SwedenPostShippingService
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
        if(in_array($shippingService->service_sub_class ,[ShippingService::DirectLinkAustralia,ShippingService::DirectLinkCanada,ShippingService::DirectLinkMexico , ShippingService::DirectLinkChile, ShippingService::Prime5 ,ShippingService::Prime5RIO]) && $this->weight <= $shippingService->max_weight_allowed)
        {
            return true;
        }
    }

    public function isAvailableForInternational($shippingService)
    {
        if(in_array($shippingService->service_sub_class ,[ShippingService::DirectLinkAustralia,ShippingService::DirectLinkCanada,ShippingService::DirectLinkMexico , ShippingService::DirectLinkChile, ShippingService::Prime5 ,ShippingService::Prime5RIO]) && $this->weight <= $shippingService->max_weight_allowed)
        {
            return true;
        }
    }



}
