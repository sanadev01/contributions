<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class BulkActionController extends Controller
{
    public function __invoke(Request $request, OrderRepository $orderRepository)
    {
        $orderIds = array_map( function($id) { return decrypt($id);
        }, json_decode($request->get('data'),true));
        $orders = $orderRepository->getOrderByIds($orderIds);
        
        return view('admin.orders.label.bulk-print',compact('orders'));
    }
}
