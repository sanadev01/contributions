<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Services\PostPlus\CN35LabelMaker;
use App\Services\PostPlus\CN35LabelHandler;

class PostPlusCN35DownloadController extends Controller
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
        if ($order) {
            $orderWeight = $order->getOriginalWeight('kg');
        }
        $cn35Maker = new CN35LabelMaker();
        $cn35Maker->setDispatchNumber($container->dispatch_number)
            ->setService($container->service_code)
            ->setDispatchDate(Carbon::now()->format('Y-m-d'))
            ->setSerialNumber(1)
            ->setOriginAirport('MIA')
            ->setType($orderWeight)
            ->setDestinationAirport($container->destination_ariport)
            ->setWeight($container->total_weight)
            ->setItemsCount($container->total_orders)
            ->setUnitCode($container->unit_code);

        return $cn35Maker->download();

        // return CN35LabelHandler::handle($container, $id);
    }
}
