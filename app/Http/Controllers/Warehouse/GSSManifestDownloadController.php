<?php

namespace App\Http\Controllers\Warehouse;

use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use Illuminate\Support\Facades\Storage;
use App\Services\GSS\GSSShipment;

class GSSManifestDownloadController extends Controller
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

        $container = $deliveryBill->containers->first();

        $shipment = json_decode($container->unit_response_list)->cn35;
        $response = (new GSSShipment($container))->getManifest($shipment->id);

        $streamFileData = $response->getBody()->getContents();
        $fileName = "{$container->awb}.csv";
        $headers = [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
        ];

        $label = response( $streamFileData, 200, $headers );
        Storage::put("labels/{$fileName}", $label);
        return $label;
 
    }
}
