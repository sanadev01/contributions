<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Excel\Export\ExportWhiteLabelManifest;

class GDEManifestDownloadController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill)
    {
        $exportService =  new ExportWhiteLabelManifest($deliveryBill); 
        $exportService->handle();
        return $exportService->download();
    }
}
