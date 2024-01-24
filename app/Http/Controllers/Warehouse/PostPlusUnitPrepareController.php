<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\PostPlus\PostPlusShipment;

class PostPlusUnitPrepareController extends Controller
{
    public function __invoke(Container $container)
    {
        $containers = Container::where('awb', $container->awb)->get();
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger','Please add parcels to this container');
            return back();
        }

        $response =  (new PostPlusShipment($container))->create();
        $data = $response->getData();
        if ($data->isSuccess) {
            foreach($containers as $package) {
                $package->update([
                    'sequence' => $data->output
                ]); 
            }
            session()->flash('alert-success', $data->message);
            return back();
        } else {
            session()->flash('alert-danger',$data->message);
            return back();
        } 
    }
}
