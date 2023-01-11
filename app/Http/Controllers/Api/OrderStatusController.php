<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use App\Mail\Admin\NotifyTransaction;
use App\Http\Controllers\Controller;
use DB;
use Exception;

class OrderStatusController extends Controller
{
    public function __invoke(Request $request)
    {
        $order = Order::find($request->order_id);
        $user = $request->user;
        $preStatus = $order->status_name;
       

        if($order->status == Order::STATUS_REFUND){
            return apiResponse(false,"You can't change status anymore");
        }
        
        if ( $order ){
            if($request->status == Order::STATUS_REFUND && $order->isPaid()){
                DB::beginTransaction();
                try{
                    $deposit = Deposit::create([
                        'uuid' => PaymentInvoice::generateUUID('DP-'),
                        'amount' => $order->gross_total,
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'last_four_digits' => 'credit from cancelation, refund '.$order->warehouse_number,
                        'balance' => Deposit::getCurrentBalance($order->user) + $order->gross_total,
                        'is_credit' => true,
                    ]);
            
                    if ($order){
                        $order->deposits()->sync($deposit->id);
                    }
                    
                    $order->update([
                        'status'  => $request->status,
                        'is_paid' => false
                    ]);                
                    $this->sendTransactionMail($deposit, $preStatus, $user);
                    DB::commit();

                }catch(Exception $e){
                    DB::rollBack(); 
                    return apiResponse(false,$e->getMessage()); 
                }
   
                           
                return apiResponse(true,"Updated");
            }
           
            if($request->status == Order::STATUS_PAYMENT_DONE && !$order->is_paid){
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
                    
                    if ( $order){
                        $order->deposits()->sync($deposit->id);
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
            //SendMailNotification
            $this->sendTransactionMail($deposit, $preStatus, $user);

            return apiResponse(true,"Updated");
        }

        return apiResponse(false,"Error while update");

    }

    private function sendTransactionMail($deposit, $preStatus, $user){
        try {
            \Mail::send(new NotifyTransaction($deposit, $preStatus, $user));
        } catch (\Exception $ex) {
            \Log::info('Notify Transaction email send error: '.$ex->getMessage());
        }
    }
}
