<?php

namespace App\Traits;

use App\Models\ShippingService;

trait ContainerOrderValidation
{
    protected function isValidOrder($serviceSubClass)
    {
        return $this->shippingService->service_sub_class == $serviceSubClass;
    }
    protected function orderValidate()
    {
        $validate =  [
            "NX" => $this->isValidOrder(ShippingService::Packet_Standard),
            "IX" => $this->isValidOrder(ShippingService::Packet_Express),
            "XP" => $this->isValidOrder(ShippingService::Packet_Mini),
            "BCN-NX" => $this->isValidOrder(ShippingService::BCN_Packet_Standard),
            "BCN-IX" => $this->isValidOrder(ShippingService::BCN_Packet_Express),
            "AJ-NX" => $this->isValidOrder(ShippingService::AJ_Packet_Standard),
            "AJ-IX" => $this->isValidOrder(ShippingService::AJ_Packet_Express),
            "AJC-NX" => $this->isValidOrder(ShippingService::AJ_Standard_CN),
            "AJC-IX" => $this->isValidOrder(ShippingService::AJ_Express_CN),
        ];
        return $validate[$this->container->services_subclass_code];
    }
    protected function orderValidateMessage($container, $shippingSubClass)
    {
        $packets = [
            ShippingService::Packet_Standard       => 'Packet Standard',
            ShippingService::Packet_Express        => 'Packet Express',
            ShippingService::Packet_Mini           => 'Packet Mini',
            ShippingService::BCN_Packet_Standard   => 'BCN Packet Standard',
            ShippingService::BCN_Packet_Express    => 'BCN Packet Express',
            ShippingService::AJ_Standard_CN        => 'AJ Standard CN',
            ShippingService::AJ_Express_CN         => 'AJ Express CN',
            ShippingService::AJ_Packet_Standard    => 'AJ Packet Standard',
            ShippingService::AJ_Packet_Express     => 'AJ Packet Express',
        ];
        $subClass = $container->getServiceSubClass();
        return $this->validationError404("Please Check Packet Service; Container is $subClass and you put ($packets[$shippingSubClass]) Packet");
    }
    public function isValidContainerOrder()
    {
        $subString = strtolower(substr($this->barcode, 0, 2));
        if (in_array($subString, ['na', 'xl', 'nc', 'nb'])) {
            $subString = 'nx';
        }
        return strtolower($this->container->getSubClassCode())  == $subString;
    }
}
