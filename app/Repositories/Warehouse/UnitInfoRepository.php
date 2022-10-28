<?php 

namespace App\Repositories\Warehouse;

use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;


class UnitInfoRepository
{

    public function getUnitInfo($request)
    {
        $startDate  = $request->start_date.'T00:00:00-03:00';
        $endDate    = $request->end_date.'T23:59:59-03:00';
        //$url        = "/packet/v1/units/arrival?initialDate=2022-06-11T00:00:00-03:00&finalDate=2022-06-18T23:59:59-03:00&page=0";
        if($request->type == 'units_arrival'){
            $url        = "/packet/v1/units/arrival?initialDate=$startDate&finalDate=$endDate:00&page=0";
        }
        if($request->type == 'units_return'){
            $url        = "packet/v1/returning-units/available";
        }
        if($request->type == 'confirm_departure'){
            $url        = "packet/v1/returning-units/confirmed-departure?initialDepartureDate=$startDate&finalDepartureDate=$endDate:00&page=0";
        }
        // if($request->type == '3'){
        //     $url        = "packet/v1/units/arrival?initialDate=$startDate&finalDate=$endDate:00&page=0";
        // }

        $client = new Client();
        $response = $client->unitInfo($url);
        return $response;
        if ( $response instanceof PackageError){
            session()->flash('alert-danger',$response->getErrors());
            return back();
        }

    }

}