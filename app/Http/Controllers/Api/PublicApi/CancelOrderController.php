<?php

namespace App\Http\Controllers\Api\PublicApi;

use DB;
use Exception;
use App\Models\Order;
use App\Models\Deposit;
use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\NotifyTransaction;

class CancelOrderController extends Controller
{
    public function __invoke(Order $order)
    {
        $message = 'No action to performed';
        $user = $order->user;
        if(Auth::id() != $user->id){
            return apiResponse(false,'No order found');
        }
        $preStatus = $order->status_name;  
        
        if ( $order ){
            if($order->isShipped())
            {
                $message = 'Order already shipped';
            }
            else if ($order->isRefund())
            {
                $message = "Order already refunded";
            }
            else if($order->isPaid()){

                DB::beginTransaction();
                try{
                    $deposit = Deposit::create([
                        'uuid'             => PaymentInvoice::generateUUID('DP-'),
                        'amount'           => $order->gross_total,
                        'user_id'          => $order->user_id,
                        'order_id'         => $order->id,
                        'last_four_digits' => 'credit from cancelation, refund '.$order->warehouse_number,
                        'balance'          => Deposit::getCurrentBalance($order->user) + $order->gross_total,
                        'is_credit'        => true,
                    ]);
                    if($order){
                        $order->deposits()->sync($deposit->id);
                        $order->update([
                                'status' => Order::STATUS_REFUND,
                                'is_paid' =>  false
                            ]);
                    }
                            try {          
                                //SendMailNotification
                                Mail::send(new NotifyTransaction($deposit, $preStatus, Auth::user()->name));
                            } catch (Exception $ex) { 
                                Log::info('Notify Transaction email send error: '.$ex->getMessage());
                            }
                DB::commit();
                }catch(Exception $e){
                    DB::rollBack(); 
                    return response()->json(['message'=>$ex->getMessage(),'line'=>$ex->getLine()],422); 
 
                }
                $message = "Order Refund & Cancelled";
            }else{
                $order->update([
                    'status' => Order::STATUS_CANCEL,
                    'is_paid' =>  false
                ]);  
                $message = "Order Cancelled";
            }
            return apiResponse(true,$message); 
        }
        return apiResponse(false,"Order not found!");

    }
}
