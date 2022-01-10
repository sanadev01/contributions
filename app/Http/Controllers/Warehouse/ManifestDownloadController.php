<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Excel\Export\ExportManfestService;
use Illuminate\Http\Request;

class ManifestDownloadController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill)
    {
        $exportService = new ExportManfestService($deliveryBill);
        return $exportService->handle();
    }
}
