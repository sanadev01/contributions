<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function __invoke(Request $request)
    {
        $order = Order::find($request->order_id);

        if ( $order ){
            $order->update([
                'status' => $request->status,
                'is_paid' => $request->status >= Order::STATUS_PAYMENT_DONE ? true: false
            ]);

            return apiResponse(true,"Updated");
        }

        return apiResponse(false,"Error while update");

    }
}
