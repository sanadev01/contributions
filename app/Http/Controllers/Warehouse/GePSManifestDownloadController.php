<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\GePS\Client;
use Carbon\Carbon;
use PDF;
use Illuminate\Http\Request;

class GePSManifestDownloadController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill)
    {
        if ($deliveryBill->containers->isEmpty()) {
            return redirect()->back()->with('error', 'please add a container to this delivery bill');
        } 

        if($deliveryBill->containers[0]->services_subclass_code == '537') {
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
