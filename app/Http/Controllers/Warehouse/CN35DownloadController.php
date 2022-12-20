<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\Correios\Services\Brazil\CN35LabelMaker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CN35DownloadController extends Controller
{
    public function __invoke(Container $container)
    {
        $order = $container->orders->first();
        if($order){
            $orderWeight = $order->getOriginalWeight('kg');
        }
        $cn23Maker = new CN35LabelMaker();
        $cn23Maker->setDispatchNumber($container->dispatch_number)
                     ->setService($container->getServiceCode())
                     ->setDispatchDate(Carbon::now()->format('Y-m-d'))
                     ->setSerialNumber(1)
                     ->setOriginAirport('MIA')
                     ->setType($orderWeight)
                     ->setDestinationAirport($container->getDestinationAriport())
                     ->setWeight($container->getWeight())
                     ->setItemsCount($container->getPiecesCount())
                     ->setUnitCode($container->getUnitCode()); 
        if($container->hasAnjunService()){
          $cn23Maker->setCompanyName('ANJUNLOG');
        }
        return $cn23Maker->download();
    }
}
