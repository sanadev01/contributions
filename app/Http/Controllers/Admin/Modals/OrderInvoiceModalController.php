<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderInvoiceModalController extends Controller
{
    public function __invoke(Order $order)
    {
        return view('admin.modals.orders.invoice',compact('order'));
    }
}
