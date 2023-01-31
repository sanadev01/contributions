<?php

namespace App\Http\Controllers\Admin\Order;

use App\Events\OrderReport;
use App\Jobs\ExportOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use App\Services\Excel\Export\OrderExport;

class OrderExportController extends Controller
{
    public function __invoke(Request $request, OrderRepository $orderRepository)
    {
        ExportOrder::dispatch($request);
        // event(new OrderReport($request));
        dd(132);
        $orders = $orderRepository->getOdersForExport($request);
        
        $exportService = new OrderExport($orders);
        return $exportService->handle();
    }
}
