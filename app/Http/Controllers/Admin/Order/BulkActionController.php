<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class BulkActionController extends Controller
{
    public function __invoke(Request $request)
    {
        $orderIds = json_decode($request->get('data'),true);
        $orders = Order::find($orderIds);
        
        return view('admin.orders.label.bulk-print',compact('orders'));
    }
}
