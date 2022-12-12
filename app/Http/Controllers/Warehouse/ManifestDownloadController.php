<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Excel\Export\ExportManfestService;
use App\Services\Excel\Export\ExportManfestByServices;

class ManifestDownloadController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill,Request $request)
    {
        dd($request);
        $exportService =  $request->service ? new ExportManfestByServices($deliveryBill) : new ExportManfestService($deliveryBill);
        return $exportService->handle();
    }
}
