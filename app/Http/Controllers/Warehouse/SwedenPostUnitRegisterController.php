<?php

namespace App\Http\Controllers\Warehouse;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Services\SwedenPost\Services\Container\DirectLinkReceptacle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SwedenPostUnitRegisterController extends Controller
{
    public function __invoke(Container $container)
    {
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger','Please add parcels to this container');
            return back();
        }

        $response =  (new DirectLinkReceptacle($container))->close();
        $data = $response->getData();

        if ($data->isSuccess){
            $container->update([
                'unit_response_list' => json_encode(['cn35'=>$data->output]),
                'response' => '1',
            ]); 
            session()->flash('alert-success', $data->message);
            return back();
              
        } else {
            session()->flash('alert-danger',$data->message);
            return back();
        } 
    }

}
