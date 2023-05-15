<?php

namespace App\Http\Controllers\Warehouse;

use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\GePS\Client;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Excel\Export\ExportWhiteLabelManifest;

class GDEManifestDownloadController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill)
    {
        $exportService =  new ExportWhiteLabelManifest($deliveryBill);
        return $exportService->handle();
    }
}
