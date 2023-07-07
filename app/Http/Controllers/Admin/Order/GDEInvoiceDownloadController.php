<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Services\Excel\Export\ExportGDEInvoice;
use Illuminate\Http\Request;

class GDEInvoiceDownloadController extends Controller
{
    public function __invoke(Order $order)
    {
        $exportService =  new ExportGDEInvoice($order);
        $exportService->handle();
        return $exportService->download();
    }
}
