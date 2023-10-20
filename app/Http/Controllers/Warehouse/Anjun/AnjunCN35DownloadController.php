<?php

namespace App\Http\Controllers\Warehouse\Anjun;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\Correios\Services\Brazil\CN35LabelMaker;
use Carbon\Carbon;
use App\Models\Warehouse\Container as WarehouseContainer;
class AnjunCN35DownloadController extends Controller
{
    public function __invoke(Container $container)
    {
        $response = json_decode($container->unit_response_list);
        $cn23Maker = new CN35LabelMaker($container);
         
        $packetType = ($container->services_subclass_code == WarehouseContainer::CONTAINER_ANJUNC_IX) ? "PACKET EXPRESS" : "PACKET STANDARD";
        $cn23Maker =   $cn23Maker->setDispatchNumber($response->id)
            ->setDestinationAirport($response->cdes)
            ->setOriginAirport($response->cfrom)
            ->setPacketType($packetType)
            ->setCompanyName('ANJUNLOG')
            ->setDispatchDate(Carbon::now()->format('Y-m-d'));

        return $cn23Maker->download();
    }
}
