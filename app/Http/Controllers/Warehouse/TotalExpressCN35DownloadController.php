<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Services\TotalExpress\CN35\CN35LabelMaker;
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
        if (request()->get('type') == 'hd') {
            $order = $container->orders->first();
            if ($order) {
                $orderWeight = $order->getOriginalWeight('kg');
            }
            $cn23Maker = new CN35LabelMaker();
            $cn23Maker->setDispatchNumber($container->dispatch_number)
                ->setDispatchDate(Carbon::now()->format('Y-m-d'))
                ->setSerialNumber(1)
                ->setOriginAirport('BR')
                ->setType($orderWeight)
                ->setDestinationAirport($container->destination_ariport)
                ->setWeight($container->total_weight)
                ->setItemsCount($container->total_orders)
                ->setUnitCode($container->unit_code);
            // if($container->has_anjun_service){
            $cn23Maker->setCompanyName('TotalExpress');
            $cn23Maker->packetType = 'Total Express';

            // }
            return $cn23Maker->download();
        }

        if ($container->unit_response_list == null) {
            abort(403, 'Overpack not register.');
        }

        $client = new Client();
        $response =  $client->overpackLabel($container);

        session()->flash($response['type'], $response['message']);
        return back();
    }
}
