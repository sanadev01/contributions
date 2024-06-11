<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Services\GSS\CN35LabelHandler;

class GSSCN35DownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container)
    {
        return CN35LabelHandler::handle($container);
    }

}
