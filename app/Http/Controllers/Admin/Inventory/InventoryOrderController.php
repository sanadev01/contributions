<?php

namespace App\Http\Controllers\Admin\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Excel\Export\OrderExport;
use App\Repositories\Inventory\OrderRepository;

class InventoryOrderController extends Controller
{

    public function index(Request $request)
    {
        return view('admin.inventory.order.index');
    }

    public function exportOrders(Request $request, OrderRepository $orderRepository)
    {
        $orders = $orderRepository->getOdersForExport($request);
        
        $exportService = new OrderExport($orders);
        return $exportService->handle();
    }
}
