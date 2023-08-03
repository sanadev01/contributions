<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\GePS\CN35LabelMaker;
use Carbon\Carbon;

use App\Services\TotalExpress\Client;
class TotalExpressCN35DownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container)
    {
        // dd($container->unit_response_list); 
        if ( $container->unit_response_list == null ){
            abort(403,'Overpack not register.');
        }
         
        $client = new Client();
        $response =  $client->overpackLabel($container); 
       
        session()->flash($response['type'],$response['message']);
        return back();
        
    }
}
