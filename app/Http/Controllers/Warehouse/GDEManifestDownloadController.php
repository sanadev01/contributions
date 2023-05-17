<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Excel\Export\ExportUsCustomerLabelManifest;
use App\Services\Excel\Export\ExportWhiteLabelManifest;
use Illuminate\Http\Request;

class GDEManifestDownloadController extends Controller
{
    public function __invoke(Request $request,DeliveryBill $deliveryBill)
    {
        if($request->type=='us-customers'){
            $exportService =  new ExportUsCustomerLabelManifest($deliveryBill);
        }
        else{ 
            $exportService =  new ExportWhiteLabelManifest($deliveryBill);
        }
            $exportService->handle();
            return $exportService->download();
    }
}
