<?php

namespace App\Http\Controllers\Warehouse\Anjun;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\Correios\Services\Brazil\CN35LabelMaker;
use Carbon\Carbon;

class AnjunCN35DownloadController extends Controller
{
    public function __invoke(Container $container)
    {
        $response = json_decode($container->unit_response_list);
        $cn23Maker = new CN35LabelMaker($container);
         
        $cn23Maker =   $cn23Maker->setDispatchNumber($response->id)
            ->setDestinationAirport($response->cdes)
            ->setOriginAirport($response->cfrom)
            ->setCompanyName('ANJUNLOG');

        return $cn23Maker->download();
    }
}
