<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use App\Mail\Admin\NotifyTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class OrderStatusController extends Controller
{
    public function __invoke(Request $request)
    {
        $order = Order::find($request->order_id);
        $user = $request->user;
        
        if($order->status == Order::STATUS_PREALERT_TRANSIT) {
            $preStatus = "STATUS_PREALERT_TRANSIT";
        }elseif($order->status == Order::STATUS_PREALERT_READY){
            $preStatus = "STATUS_PREALERT_READY";
        }elseif($order->status == Order::STATUS_ORDER){
            $preStatus = "STATUS_ORDER";
        }elseif($order->status == Order::STATUS_NEEDS_PROCESSING){
            $preStatus = "STATUS_NEEDS_PROCESSING";
        }elseif($order->status == Order::STATUS_PAYMENT_PENDING){
            $preStatus = "STATUS_PAYMENT_PENDING";
        }elseif($order->status == Order::STATUS_PAYMENT_DONE){
            $preStatus = "STATUS_PAYMENT_DONE";
        }else {
            $preStatus = '';
        }

        if($order->status == Order::STATUS_REFUND){
            return apiResponse(false,"You can't change status anymore");
        }
        
        \Log::info('Login user id: ' . Auth::id());
        \Log::info('order user id: ' . $order->user_id);
        \Log::info('order id: ' . $order->id);
        \Log::info('order status: ' . $order->status);
        \Log::info('request status: ' . $request->status);
        
        if ( $order ){
            if($request->status == Order::STATUS_REFUND && $order->isPaid()){
                $deposit = Deposit::create([
                    'uuid' => PaymentInvoice::generateUUID('DP-'),
                    'amount' => $order->gross_total,
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'last_four_digits' => 'credit from cancelation, refund '.$order->warehouse_number,
                    'balance' => Deposit::getCurrentBalance($order->user) + $order->gross_total,
                    'is_credit' => true,
                ]);
        
                if ( $order ){
                    $order->deposits()->sync($deposit->id);
                }

                try {
                    \Mail::send(new NotifyTransaction($deposit, $preStatus, $user));
                } catch (\Exception $ex) {
                    \Log::info('Notify Transaction email send error: '.$ex->getMessage());
                }
                
                $order->update([
                    'status'  => $request->status,
                    'is_paid' => false
                ]);

                                
                return apiResponse(true,"Updated");
            }
           
            if($request->status == Order::STATUS_PAYMENT_DONE && !$order->is_paid){
                \Log::info('status: ' . 'STATUS_PAYMENT_DONE');
                if(Deposit::getCurrentBalance($order->user) >= $order->gross_total){
                    $deposit = Deposit::create([
                        'uuid' => PaymentInvoice::generateUUID('DP-'),
                        'amount' => $order->gross_total,
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'last_four_digits' => 'admin Debit '.$order->warehouse_number,
                        'balance' => Deposit::getCurrentBalance($order->user) - $order->gross_total,
                        'is_credit' => false,
                    ]);
                    
                    if ( $order ){
                        $order->deposits()->sync($deposit->id);
                    }

                    try {
                        \Mail::send(new NotifyTransaction($deposit, $preStatus, $user));
                    } catch (\Exception $ex) {
                        \Log::info('Notify Transaction email send error: '.$ex->getMessage());
                    }

                }else{
                    return apiResponse(false,"Not Enough Balance. Please Add Balance to ".$order->user->name.' '. $order->user->pobox_number ." account.");
                }
            }

            if($order->isPaid()){
                $order->update([
                    'status' => $request->status,
                ]);

                
                return apiResponse(true,"Updated");
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
