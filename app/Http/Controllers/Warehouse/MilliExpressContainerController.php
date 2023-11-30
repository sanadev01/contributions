<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\MileExpress\CN35LabelMaker;

class MilliExpressContainerController extends Controller
{
    public function __invoke(Container $container)
    { 
        return (new CN35LabelMaker($container))->download();
    }
}
