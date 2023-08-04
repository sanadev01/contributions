<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
// use App\Services\GePS\CN35LabelMaker;
use App\Services\Correios\Services\Brazil\CN35LabelMaker;
use Carbon\Carbon;

use App\Services\TotalExpress\Client;
class TotalExpressCN35DownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container)
    {
        if(request()->get('type')=='hd'){
            $order = $container->orders->first();
            if($order){
                $orderWeight = $order->getOriginalWeight('kg');
            }
            $cn23Maker = new CN35LabelMaker();
            $cn23Maker->setDispatchNumber($container->dispatch_number)
                         ->setService($container->getServiceCode())
                         ->setDispatchDate(Carbon::now()->format('Y-m-d'))
                         ->setSerialNumber(1)
                         ->setOriginAirport('BR')
                         ->setType($orderWeight)
                         ->setDestinationAirport($container->getDestinationAriport())
                         ->setWeight($container->getWeight())
                         ->setItemsCount($container->getPiecesCount())
                         ->setUnitCode($container->getUnitCode()); 
            // if($container->hasAnjunService()){
              $cn23Maker->setCompanyName('TotalExpress');
              $cn23Maker->setService();
            // }
            return $cn23Maker->download();
        }

        if ( $container->unit_response_list == null ){
            abort(403,'Overpack not register.');
        }
         
        $client = new Client();
        $response =  $client->overpackLabel($container); 
       
        session()->flash($response['type'],$response['message']);
        return back();
        
    }
}
