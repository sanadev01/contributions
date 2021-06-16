<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderStatusController extends Controller
{
    public function __invoke(Request $request)
    {
        $order = Order::find($request->order_id);

        if ( $order ){
            if($request->status == Order::STATUS_REFUND && $order->status != Order::STATUS_REFUND && $order->status == Order::STATUS_PAYMENT_DONE){
                $deposit = Deposit::create([
                    'uuid' => PaymentInvoice::generateUUID('DP-'),
                    'amount' => $order->gross_total,
                    'user_id' => $order->user_id,
                    'balance' => Deposit::getCurrentBalance($order->user) + $order->gross_total,
                    'is_credit' => true,
                ]);
        
                if ( $order ){
                    $order->deposits()->sync($deposit->id);
                }
            }
            
            if($request->status >= Order::STATUS_PAYMENT_DONE && $order->status == Order::STATUS_REFUND ){
                $deposit = Deposit::create([
                    'uuid' => PaymentInvoice::generateUUID('DP-'),
                    'amount' => $order->gross_total,
                    'user_id' => $order->user_id,
                    'balance' => Deposit::getCurrentBalance($order->user) - $order->gross_total,
                    'is_credit' => false,
                ]);
        
                if ( $order ){
                    $order->deposits()->sync($deposit->id);
                }
            }
            $order->update([
                'status' => $request->status,
                'is_paid' => $request->status >= Order::STATUS_PAYMENT_DONE ? true: false
            ]);

            return apiResponse(true,"Updated");
        }

        return apiResponse(false,"Error while update");

    }
}
