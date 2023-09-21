<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\SwedenPost\Services\Container\CN35CountriesLabel;
use Carbon\Carbon;

use App\Services\TotalExpress\Client;
class SwedenPostCN35DownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container)
    {
        $order = $container->orders->first();
        if($order){
            $orderWeight = $order->getOriginalWeight('kg');
        }
        
        // $destinationAirport = 'EWR';
        // if($order->recipient->country->code == 'MX' && $order->taxModility == "DDP"){
        //     $destinationAirport = "LRD";
        // }

    $cn23Maker = new CN35CountriesLabel();
        $cn23Maker->setDispatchNumber($container->dispatch_number)
                     ->setDispatchDate(Carbon::now()->format('Y-m-d'))
                     ->setSerialNumber(1)
                     ->setOriginAirport('MIA')
                     ->setType($orderWeight)
                     ->setDestinationAirport($order->recipient->country->code)
                     ->setWeight($container->getWeight())
                     ->setItemsCount($container->getPiecesCount())
                     ->setUnitCode($container->getUnitCode()); 
        // if($container->hasAnjunService()){
          $cn23Maker->setCompanyName('DirectLink'); 
          $cn23Maker->packetType = "Direct Link";
        // }
        return $cn23Maker->download();
        
    }
}