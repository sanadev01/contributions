<?php

namespace App\Http\Controllers\Warehouse;

use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;

class PostPlusManifestDownloadController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill)
    {
        if ($deliveryBill->containers->isEmpty()) {
            session()->flash('alert-danger',  'please add a container to this delivery bill');
            return back()->withInput();
        }
        
        if(!$deliveryBill->request_id) {
            session()->flash('alert-danger','Delivery not registered yet. Please register delivery bill first to download manifest');
            return back()->withInput();
        }
 
    }
}
