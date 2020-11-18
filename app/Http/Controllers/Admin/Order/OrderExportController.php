<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Excel\Export\OrderExport;
use App\Repositories\OrderRepository;

class OrderExportController extends Controller
{
    public function __invoke(OrderRepository $orderRepository)
    {
        $orders = $orderRepository->getOdersForExport();
        
        $exportService = new OrderExport($orders);
        return $exportService->handle();
    }
}
