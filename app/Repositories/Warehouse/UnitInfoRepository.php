<?php 

namespace App\Repositories\Warehouse;

use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;


class UnitInfoRepository
{

    public function getUnitInfo($request)
    {
        $startDate  = $request->start_date.' 00:00:00-03:00';
        $endDate    = $request->end_date.' 23:59:59-03:00';
        $url        = "/packet/v1/units/arrival?initialDate=2022-10-22T00:00:00-03:00&finalDate=2022-10-28T23:59:59-03:00&page=0";

        // if($request->type == '1'){
        //     $url        = "packet/v1/units/arrival?initialDate=$startDate&finalDate=$endDate:00&page=0";
        // }
        // if($request->type == '2'){
        //     $url        = "packet/v1/units/arrival?initialDate=$startDate&finalDate=$endDate:00&page=0";
        // }
        // if($request->type == '3'){
        //     $url        = "packet/v1/units/arrival?initialDate=$startDate&finalDate=$endDate:00&page=0";
        // }

        $client = new Client();
        $response = $client->unitInfo($url);
        dd(json_decode($response));
        if ( $response instanceof PackageError){
            session()->flash('alert-danger',$response->getErrors());
            return back();
        }

    }

}