<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Excel\Export\OrderExport;
use App\Repositories\OrderRepository;

class OrderExportController extends Controller
{
    public function __invoke(OrderRepository $orderRepository)
    {
        $orders = $orderRepository->getOdersForExport();
        
        $ExportService = new OrderExport($orders);
        return $ExportService->handle();
    }
}
