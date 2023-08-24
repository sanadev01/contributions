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
    public function addFlightDetails(Request $request)
    {
        $deliveryBill = DeliveryBill::find($request->id);
        
        if ($deliveryBill->containers->isEmpty()) {
            session()->flash('alert-danger',  'please add a container to this delivery bill');
            return back()->withInput();
        }
        
        $container = $deliveryBill->containers->first();

        $apiRequest = (new TotalExpressMasterBox($container))->createFlight($deliveryBill, $request);
        session()->flash($apiRequest['type'],$apiRequest['message']);
        return back();
 
    }

    public function closeManifest(DeliveryBill $deliveryBill, Request $request) {
        //
    }

    public function downloadManifest (DeliveryBill $deliveryBill) {

        if(!$deliveryBill->request_id) {
            session()->flash('alert-danger','Delivery not registered yet. Please register delivery bill first to download manifest');
            return back()->withInput();
        }
        
    }
}
