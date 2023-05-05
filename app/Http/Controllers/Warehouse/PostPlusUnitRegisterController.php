<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\PostPlus\PostPlusShipment;

class PostPlusUnitRegisterController extends Controller
{
    public function __invoke(Container $container)
    {
        $containers = Container::where('awb', $container->awb)->get();

        $response =  (new PostPlusShipment($container))->prepareShipment($container->sequence);
        $data = $response->getData();
        if ($data->isSuccess){
            $updateShipment = (new PostPlusShipment($container))->getShipmentDetails($data->output->id);
            $shipmentDetails = $updateShipment->getData();
            if(isset($shipmentDetails->output->bags[0]->outboundBagNrs)) {
                foreach($containers as $package) {
                    $package->update([
                        'unit_response_list' => json_encode(['cn35'=>$shipmentDetails->output]),
                        'unit_code' => optional(optional(optional($shipmentDetails)->output)->bags)[0]->outboundBagNrs,
                        'response' => '1',
                    ]); 
                }
            } else {
                session()->flash('alert-danger',"Container Registration No. Returned Empty by the Post Plus Api");
                return back();
            }

            //Create Delivery Bill
            $containerIds = [];
            foreach ($containers as $key => $container) {
                $idsToPush = [$container->id,];
                array_push($containerIds, $idsToPush);
            }
            $idsArray = array_merge(...$containerIds);

            $deliveryBill = DeliveryBill::create([
                'name' => 'Delivery Bill: '.Carbon::now()->format('m-d-Y'),
                'request_id' => $shipmentDetails->output->id,
                'cnd38_code' => $container->awb,
            ]);

            $deliveryBill->containers()->sync($idsArray);

            foreach($deliveryBill->containers()->get() as $bills){
                $bills->orders()->update([
                    'status' =>  Order::STATUS_SHIPPED,
                    'api_tracking_status' => 'HD-Shipped',
                ]);

                foreach($bills->orders as $order)
                { 
                    $this->addOrderTracking($order->id);
                }
            }
            session()->flash('alert-success', $data->message);
            return back();
              
        } else {
            session()->flash('alert-danger',$data->message);
            return back();
        } 
    }

    public function addOrderTracking($order_id)
    {
        OrderTracking::create([
            'order_id' => $order_id,
            'status_code' => Order::STATUS_SHIPPED,
            'type' => 'HD',
            'description' => 'Parcel transfered to airline',
            'country' => 'US',
            'city' => 'Miami'
        ]);

        return true;
    }
}
