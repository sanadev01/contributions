<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\SwedenPost\Services\Container\CN35LabelHandler;
use App\Services\SwedenPost\Services\Container\CN35CountriesLabel;
use Illuminate\Support\Facades\Response; 
use Carbon\Carbon;
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
        
        if ($container->is_directlink_coutry) { 
            $cn23Maker = new CN35CountriesLabel();
            $order = $container->orders->first();
            if($order){
                $orderWeight = $order->getOriginalWeight('kg');
            }
            $cn23Maker->setDispatchNumber($container->dispatch_number)
                        //  ->setService($container->getServiceCode())
                        ->setDispatchDate(Carbon::now()->format('Y-m-d'))
                        ->setSerialNumber(1)
                        ->setOriginAirport('MIA') 
                        ->setType($orderWeight)
                        ->setDestinationAirport($container->getDestinationAriport())
                        ->setWeight($container->getWeight())
                        ->setItemsCount($container->getPiecesCount())
                        ->setUnitCode($container->getUnitCode()); 
            //   $cn23Maker->setCompanyName('DirectLink');
            
            return $cn23Maker->download();
 
        }
        $response  = CN35LabelHandler::handle($container)->getData();
        if($response->isSuccess){
           return Response::download($response->output);
        }
        else{
            session()->flash('alert-danger', $response->message);
            return back();
        }
    }

}
