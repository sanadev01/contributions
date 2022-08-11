<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\GePS\CN35LabelMaker;
use Carbon\Carbon;

class GePSCN35DownloadController extends Controller
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
        $cn35Maker = new CN35LabelMaker();
        $cn35Maker->setDispatchNumber($container->dispatch_number)
                     ->setService($container->getServiceCode())
                     ->setDispatchDate(Carbon::now()->format('Y-m-d'))
                     ->setSerialNumber(1)
                     ->setOriginAirport('MIA')
                     ->setType($orderWeight)
                     ->setDestinationAirport($container->getDestinationAriport())
                     ->setWeight($container->getWeight())
                     ->setItemsCount($container->getPiecesCount())
                     ->setUnitCode($container->getUnitCode());

        return $cn35Maker->download();
        
    }
}
