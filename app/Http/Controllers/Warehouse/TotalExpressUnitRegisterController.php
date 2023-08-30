<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\TotalExpress\Services\TotalExpressMasterBox;

class TotalExpressUnitRegisterController extends Controller
{
    public function __invoke(Container $container)
    {
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger','Please add parcels to this container');
            return back();
        }

        $client = new TotalExpressMasterBox($container);
        $request =  $client->requestMasterBox();

        $response = $request->getData();
        if ($response->isSuccess){
            session()->flash('alert-success', $response->message);
            return back();
              
        } else {
            session()->flash('alert-danger',$response->message);
            return back();
        } 
    }
}
