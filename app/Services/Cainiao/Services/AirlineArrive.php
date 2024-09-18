<?php

namespace App\Services\Cainiao\Services;
use App\Models\Warehouse\Container; 

class AirlineArrive
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
            "mawbNo"=> "44300000024",
            "flightNo"=> "8119",
            "ata"=> "2024-08-03 17:40:16",
            "atd"=> "2024-08-02 17:40:16",
            "timeZone"=> "+8"
        ]);
    } 
 
}
