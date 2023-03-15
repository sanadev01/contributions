<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\PostPlus\CN35LabelHandler;
use Illuminate\Support\Facades\Response;

class PostPlusCN35DownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container, $id)
    {
        return CN35LabelHandler::handle($container, $id);
    }

}
