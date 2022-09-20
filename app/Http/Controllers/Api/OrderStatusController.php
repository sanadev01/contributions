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
            $pre_status = "STATUS_PREALERT_TRANSIT";
        }elseif($order->status == Order::STATUS_PREALERT_READY){
            $pre_status = "STATUS_PREALERT_READY";
        }elseif($order->status == Order::STATUS_ORDER){
            $pre_status = "STATUS_ORDER";
        }elseif($order->status == Order::STATUS_NEEDS_PROCESSING){
            $pre_status = "STATUS_NEEDS_PROCESSING";
        }elseif($order->status == Order::STATUS_PAYMENT_PENDING){
            $pre_status = "STATUS_PAYMENT_PENDING";
        }else {
            $pre_status = '';
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
                
                $order->update([
                    'status'  => $request->status,
                    'is_paid' => false
                ]);

                                
                return apiResponse(true,"Updated");
            }
           
            if($request->status == Order::STATUS_PAYMENT_DONE && !$order->is_paid){
                \Log::info('status: ' . 'STATUS_PAYMENT_DONE');
                $pre_balance = Deposit::getCurrentBalance($order->user);
                if(Deposit::getCurrentBalance($order->user) >= $order->gross_total){
                    $rem_balance = Deposit::getCurrentBalance($order->user) - $order->gross_total;
                    $deposit = Deposit::create([
                        'uuid' => PaymentInvoice::generateUUID('DP-'),
                        'amount' => $order->gross_total,
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'last_four_digits' => 'admin Debit '.$order->warehouse_number,
                        'balance' => Deposit::getCurrentBalance($order->user) - $order->gross_total,
                        'is_credit' => false,
                    ]);
                    $amount = $order->gross_total;
                    $new_status = 'STATUS_PAYMENT_DONE';
                    $created = Carbon::now();
                    if ( $order ){
                        $order->deposits()->sync($deposit->id);
                    }

                    try {
                        \Mail::send(new NotifyTransaction($user, $order, $pre_status, $pre_balance, $amount, $rem_balance, $new_status, $created));
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
