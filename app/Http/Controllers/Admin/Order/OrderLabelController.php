<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\GePS\Client;
use App\Services\SwedenPost\Client as SPClient;
use App\Http\Controllers\Controller;
use App\Repositories\HandleCorreiosLabelsRepository;
use App\Repositories\SinerlogHandleRepository;

class OrderLabelController extends Controller
{
 
    public function index(Request $request, Order $order)
    {
        $this->authorize('canPrintLable',$order);
        return view('admin.orders.label.index',compact('order'));
    }


    public function store(Request $request, Order $order)
    {
        $this->authorize('canPrintLable',$order);

        if(!$order->isPaid()){
            $error = 'Error: Payment is Pending';
            $buttonsOnly = $request->has('buttons_only');
            return view('admin.orders.label.label',compact('order','error','buttonsOnly'));
        }

        if($order->recipient->country_id == Order::BRAZIL && $order->shippingService->api == 'sinerlog'){
            return (new SinerlogHandleRepository($request,$order))->handle(); 
        }
        else {
            return (new HandleCorreiosLabelsRepository($request,$order))->handle(); // 
        }         
    }    
    public function cancelLabel(Order $order)
    {
        if($order->carrierService() == "Global eParcel") {
            $gepsClient = new Client();   
            $response = $gepsClient->cancelShipment($order->corrios_tracking_code);
            if (!$response['success']) {
                session()->flash('alert-danger', $response['message']);
                return back();
            }
            if($response['success']) {
                $order->update([
                    'corrios_tracking_code' => null,
                    'cn23' => null,
                    'api_response' => null
                ]);
                session()->flash('alert-success','Shipment '.$response['data']->cancelshipmentresponse->tracknbr.' cancellation is successful. You can print new lable now.');
                return back();
            }
        }
        if($order->carrierService() == "Prime5") {
            $apiOrderId = null;
            $apiResponse = json_decode($order->api_response);
            if($apiResponse) {
                $apiOrderId = $apiResponse->data[0]->orderId;
            }
            if(!is_null($apiOrderId)) {
                $swedenpostClient = new SPClient();   
                $response = $swedenpostClient->deleteOrder($apiOrderId); 
                if (!$response['success']) {
                    session()->flash('alert-danger', $response['message']);
                    return back();
                }
                if($response['success']) {
                    $order->update([
                        'corrios_tracking_code' => null,
                        'cn23' => null,
                        'api_response' => null
                    ]);
                    session()->flash('alert-success','Shipment '.$response->data[0]->referenceNo.' cancellation is successful. You can print new lable now.');
                    return back();
                }
            } else {
                session()->flash('alert-danger','Order not found');
                return back();
            }
            
        }
    } 
}
