<?php

namespace App\Http\Controllers\Warehouse;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use Carbon\Carbon;

use App\Services\TotalExpress\Client;

class TotalExpressUnitRegisterController extends Controller
{
    public function __invoke(Container $container)
    {
       
        $containers = Container::where('awb', $container->awb)->get();
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger','Please add parcels to this container');
            return back();
        }

        $client = new Client();
        $response =  $client->registerUnit($container); 
       
            session()->flash($response['type'],$response['message']);
            return back();
              
        
 
    }
}
