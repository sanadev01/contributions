<?php

namespace App\Http\Controllers\Warehouse;

use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\GePS\Client;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;

class GDEManifestDownloadController extends Controller
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

        if($deliveryBill->containers[0]->services_subclass_code == ShippingService::GePS) {
            $client = new Client();
            $response = $client->downloadGePSManifest($deliveryBill);

            if ($response['success'] == false) {
                session()->flash('alert-danger', $response['message']);
                return back();
            }
            $result = $response['data']->manifestresponse;
            $manifest_pdf = base64_decode($result->manifestpdf);
            $path = storage_path("{$deliveryBill->cnd38_code}.pdf");
            file_put_contents($path, $manifest_pdf); //store file temporarily
            return response()->download($path)->deleteFileAfterSend(true); //download file and delete it

        }        
    }
}
