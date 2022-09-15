<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\PostNL\PostNLLabelMaker;

class PostNLCN35DownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container)
    {
        $labelPrinter = new PostNLLabelMaker();
        return $labelPrinter->getContainerCN35($container->unit_response_list);

    }
}
