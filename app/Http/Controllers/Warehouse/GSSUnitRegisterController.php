<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\GSS\Client;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;

class GSSUnitRegisterController extends Controller
{
    public function __invoke(Container $container)
    {
        $containers = Container::where('awb', $container->awb)->get();
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger','Please add parcels to this container');
            return back();
        }

        $client = new Client();
        $response =  $client->createReceptacle($container);
        $data = $response->getData();
        if ($data->isSuccess){
            session()->flash('alert-success', $data->message);
            return back();
              
        } else {
            session()->flash('alert-danger',$data->message);
            return back();
        } 
    }
}
