<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductModalController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Order $order)
    {
        return view('admin.modals.orders.products',compact('order'));
    }
}
