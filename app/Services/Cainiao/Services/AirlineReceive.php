<?php

namespace App\Services\Cainiao\Services;
use App\Models\Warehouse\Container; 

class AirlineReceive
{ 
    protected $request; 

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function getRequestBody()
    {
        

      
        return ([ 
                "locale"=> "zh_CN",
                "mawbNo"=> "testmawbNo",
                "airlineParam"=> [
                    "flightNo"=> "121566",
                    "eta"=> "2024-06-27 00:00:00",
                    "etd"=> "2024-06-24 00:00:00",
                    "fromPortCode"=> "s",
                    "transitPortCode"=> "qw",
                    "toPortCode"=> "sdf"
                ],
                "bigBagList"=> [$this->request->unitCode],
                "weightUnit"=> "g",
                "skipMawbFiles"=> "",
                "picUrls"=> "",
                "shipmentWeight"=> "906",
                "failedParcelList"=> [],
                "mawbPieces"=> "2",
                "mawbFiles"=> [
                 ] 
        ]);
    }
}
