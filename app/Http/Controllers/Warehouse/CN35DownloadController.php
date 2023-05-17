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
        $cn23Maker = new CN35LabelMaker($container);
 
        return $cn23Maker->download();
    }
}
