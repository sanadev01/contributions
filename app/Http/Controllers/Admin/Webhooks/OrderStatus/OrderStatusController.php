<?php

namespace App\Http\Controllers\Admin\Webhooks\OrderStatus;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Observers\OrderObserver;

class OrderStatusController extends Controller
{
    public function __invoke(Request $request, OrderObserver $orderobserver)
    {
        $orderobserver->updated($request);
        
    }
}
