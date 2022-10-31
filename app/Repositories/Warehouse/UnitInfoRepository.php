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
        if($request->type == 'units_arrival'){
            $url = "/packet/v1/units/arrival?initialDate=$startDate&finalDate=$endDate&page=0";
        }
        if($request->type == 'units_return'){
            $url = "packet/v1/returning-units/available";
        }
        if($request->type == 'confirm_departure'){
            $url = "packet/v1/returning-units/confirmed-departure?initialDepartureDate=$startDate&finalDepartureDate=$endDate&page=0";
        }
        if($request->type == 'departure_info'){
            $url = "/packet/v1/returning-units";
        }

        $client = new Client();
        $response = $client->unitInfo($url, $request);
        if ( $response instanceof PackageError){
            session()->flash('alert-danger',$response->getErrors());
            return back();
        }
        return $response;

    }

}