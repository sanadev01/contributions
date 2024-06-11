<?php

namespace App\Http\Controllers\Warehouse;

use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use Illuminate\Support\Facades\Storage;
use App\Services\TotalExpress\Services\TotalExpressMasterBox;

class TotalExpressManifestController extends Controller
{
    public function createFlight(Request $request) {
        $deliveryBill = DeliveryBill::find($request->id);
        $container = $deliveryBill->containers->first();
        
        if ($deliveryBill->containers->isEmpty()) {
            session()->flash('alert-danger',  'please add a container to this delivery bill');
            return back()->withInput();
        }

        $apiRequest = (new TotalExpressMasterBox($container))->createFlight($deliveryBill, $request);
        $response = $apiRequest->getData();
        if ($response->isSuccess){
            session()->flash('alert-success', $response->message);
            return back();
              
        } else {
            session()->flash('alert-danger',$response->message);
            return back();
        }
    }
    
    public function addFlightDetails(Request $request)
    {
        $deliveryBill = DeliveryBill::find($request->id);
               
        $container = $deliveryBill->containers->first();

        $apiRequest = (new TotalExpressMasterBox($container))->updateFlightInformation($deliveryBill->cnd38_code, $request);

        session()->flash($apiRequest['type'],$apiRequest['message']);
        return back();
 
    }

    public function closeManifest(Request $request) {
        
        $deliveryBill = DeliveryBill::find($request->id);
        $container = $deliveryBill->containers->first();
        $apiRequest = (new TotalExpressMasterBox($container))->closemanifest($deliveryBill);
        $response = $apiRequest->getData();
        if ($response->isSuccess){
            session()->flash('alert-success', $response->message);
            return back();
              
        } else {
            session()->flash('alert-danger',$response->message);
            return back();
        }
    }

    public function closeFlight(Request $request) {
        
        $deliveryBill = DeliveryBill::find($request->id);
        $container = $deliveryBill->containers->first();
        $apiRequest = (new TotalExpressMasterBox($container))->consultCloseManifest($deliveryBill->request_id);
        $response = $apiRequest->getData();
        if ($response->isSuccess){
            session()->flash('alert-success', $response->message);
            return back();
              
        } else {
            session()->flash('alert-danger',$response->message);
            return back();
        }
    }

    public function downloadManifest (DeliveryBill $deliveryBill) {

        if(!$deliveryBill->request_id) {
            session()->flash('alert-danger','Delivery not registered yet. Please register delivery bill first to download manifest');
            return back()->withInput();
        }
        
    }
}
