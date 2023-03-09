<?php

namespace App\Http\Controllers\Warehouse;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\PostPlus\PostPlusShipment;

class PostPlusUnitRegisterController extends Controller
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
        if ($data->isSuccess){

            $updateShipment = (new PostPlusShipment($container))->getShipmentDetails($data->output->id);
            $shipmentDetails = $updateShipment->getData();
            foreach($containers as $package) {
                $package->update([
                    'unit_response_list' => json_encode(['cn35'=>$shipmentDetails->output]),
                    'unit_code' => $shipmentDetails->output->bags[0]->outboundBagNrs,
                    'response' => '1',
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
