<?php

namespace App\Services\Cainiao\Services;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\DeliveryBill;

class CN38Request
{ 
    protected $deliveryBill; 

    public function __construct(DeliveryBill $deliveryBill)
    {
        $this->deliveryBill = $deliveryBill;
    }

    public function getRequestBody()
    {
        $weight = $this->deliveryBill->getWeight();
        return ([
            "ULDParam" => [
                "ULDNoBatchNo" => "SNU24092024-".$this->deliveryBill->id,
                "ULDNo" => "SNU24092024",
                "ULDType" => "Q5",
                "ULDWeight" => "$weight",
                "ULDWeightUnit" => "KG",
                "bigBagList" => [
                    "bigBagTrackingNumber" => $this->bigBagTrackingNumber(),
                ]
            ],
            "airlineParam" => [
                "airlineCode" => "LA",
                "ETD" => 1722620416000,
                "transportNo" => "8119",
                "fromPortCode" => "MIA",
                "toPortCode" => "GRU"
            ],
            "operationParam" => [
                "opCode" => "0",
                "opLocation" => "DHS",
                "opTime" => date('Y-m-d H:i:s'),
                "timeZone" => "+8"
            ]
        ]);

       
    }
    // 组大包的小包LP00676355941098必须先更新实际重量，请调用小包重量更新接口
    private function bigBagTrackingNumber()
    { 
        $items = [];
        foreach ($this->deliveryBill->containers as $container) {
            $items[]=$container->unit_code; 
        }
        return $items;
    }
}
