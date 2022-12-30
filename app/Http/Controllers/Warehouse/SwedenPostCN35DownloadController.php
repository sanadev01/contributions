<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\SwedenPost\Services\Container\CN35LabelHandler;
use Illuminate\Support\Facades\Response;

class SwedenPostCN35DownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container)
    {
        $response  = CN35LabelHandler::handle($container)->getData();
        if($response->isSuccess){
           return Response::download($response->output);
        }
        else{
            session()->flash('alert-danger', $response->message);
            return back();
        }

    }

  
    

}
