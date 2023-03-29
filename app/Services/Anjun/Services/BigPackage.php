<?php

namespace App\Services\Anjun\Services;

use App\Models\Warehouse\Container as WarehouseContainer;
use App\Services\Correios\Contracts\Container;
class BigPackage
{

    public $bigBagId;
    public $serviceType   = "STANDARD";
    public $deliveryTerms = "DDU";
    public $cdes          = "GRU";
    public $cfrom         = "MIA";
    public  $trackingNumbers = [];
    public function __construct(Container $container)
    {
        $this->bigBagId      = "AJ00000" . $container->id;
        $this->serviceType   = ($container->services_subclass_code == WarehouseContainer::CONTAINER_ANJUNC_IX) ? "Express" : "STANDARD";
        foreach ($container->orders as $order) {
            $this->trackingNumbers[] = $order->corrios_tracking_code;
        }
    }
}
