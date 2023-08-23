<?php

namespace App\Http\Controllers\Warehouse;

use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use Illuminate\Support\Facades\Storage;
use App\Services\TotalExpress\Service\TotalExpressMasterBox;

class TotalExpressDownloadController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill, Request $request)
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

        $response = (new TotalExpressMasterBox($container))->createFlight($request);

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
