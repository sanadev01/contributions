<?php 

namespace App\Repositories\Warehouse;

use App\Services\Cainiao\Client as CainiaoClient;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;


class UnitInfoRepository
{

    public function getUnitInfo($request)
    {
        if($request->api=='cainiao'){
               $cainiaoClient = new CainiaoClient();
               $response = $cainiaoClient->unitInfo($request);
               if ( $response instanceof PackageError){
                   session()->flash('alert-danger',$response->getErrors());
                   return back();
               }
               return $response;
        }else{


        $startDate  = $request->start_date.'T00:00:00-03:00';
        $endDate    = $request->end_date.'T23:59:59-03:00';
        $urls = [
            'units_arrival' => "/packet/v1/units/arrival?initialDate=$startDate&finalDate=$endDate&page=0",
            'units_return' => "/packet/v1/returning-units/available",
            'confirm_departure' => "/packet/v1/returning-units/confirmed-departure?initialDepartureDate=$startDate&finalDepartureDate=$endDate&page=0",
            'departure_info' => "/packet/v1/returning-units",
            'departure_cn38' => "/packet/v1/cn38request/departure"
        ];
        
        $url = $urls[$request->type] ?? ''; // Handle case where type doesn't exist
        
        $client = new Client();
        $response = $client->unitInfo($url, $request);
        if ( $response instanceof PackageError){
            session()->flash('alert-danger',$response->getErrors());
            return back();
        }
        return $response;
        }
    }

}