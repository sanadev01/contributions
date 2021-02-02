<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderInvoiceController extends Controller
{
    public function index(Request $request, Order $order)
    {
        $this->authorize('viewInvoice',$order);
        
        if ( !$order->recipient || $order->items->isEmpty() ){
            abort(404);
        }

        return view('admin.orders.invoice.index',compact('order'));
    }

    public function store(Request $request, Order $order)
    {

    }
}
