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
        if ($order) {
            $orderWeight = $order->getOriginalWeight('kg');
        }
        $cn23Maker = new CN35LabelMaker($container);
        $cn23Maker->setDispatchNumber($container->dispatch_number)
            ->setService($container->service_code)
            ->setDispatchDate(Carbon::now()->format('Y-m-d'))
            ->setSerialNumber(1)
            ->setOriginAirport('MIA')
            ->setType($orderWeight)
            ->setDestinationAirport($container->destination_ariport)
            ->setWeight($container->total_weight)
            ->setItemsCount($container->total_orders)
            ->setUnitCode($container->unit_code);
        if ($container->has_anjun_service) {
            $cn23Maker->setCompanyName('ANJUNLOG');
        }
        return $cn23Maker->download();
    }
}
