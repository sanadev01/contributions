<?php 

namespace App\Repositories\Warehouse;


class UnitInfoRepository
{

    public function getUnitInfo($request)
    {
        $startDate  = $request->start_date.' 00:00:00-03:00';
        $endDate    = $request->end_date.' 23:59:59-03:00';
        $url        = "packet/v1/units/arrival?initialDate=$startDate&finalDate=$endDate:00&page=0";

        if($request->type == '1'){
            $url        = "packet/v1/units/arrival?initialDate=$startDate&finalDate=$endDate:00&page=0";
        }
        if($request->type == '2'){
            $url        = "packet/v1/units/arrival?initialDate=$startDate&finalDate=$endDate:00&page=0";
        }
        if($request->type == '3'){
            $url        = "packet/v1/units/arrival?initialDate=$startDate&finalDate=$endDate:00&page=0";
        }
    }

}