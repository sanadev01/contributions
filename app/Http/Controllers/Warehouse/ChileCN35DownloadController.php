<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\CorreosChile\CorreosChileCN35LabelMaker;

class ChileCN35DownloadController extends Controller
{
    public function __invoke(Container $container)
    {
        $cn35Maker = new CorreosChileCN35LabelMaker();
        $cn35Maker->setDispatchNumber($container->dispatch_number)
                    ->setService($container->getServiceCode())
                    ->setDispatchDate(Carbon::now()->format('Y-m-d'))
                    ->setSerialNumber(1)
                    ->setOriginAirport($container->origin_operator_name ? $container->origin_operator_name :'MIA')
                    ->setDestinationAirport($container->getDestinationAriport())
                    ->setWeight($container->getWeight())
                    ->setItemsCount($container->getPiecesCount())
                    ->setsealNumber($container->seal_no)
                    ->setAwbNumber($container->awb);

        return $cn35Maker->download();
    }
}
