<?php

namespace App\Http\Controllers\Admin\Order;

use Exception;
use App\Models\Order;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\Admin\NotifyTransaction;
use App\Mail\Admin\OrderNotification;

class OrderStatusController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!$request->user()->isAdmin())
            return apiResponse(false, "Only admin can change the order status");

        $order = Order::find($request->order_id);
        $user = $request->user;
        $preStatus = $order->status_name;

        if ($order->status == Order::STATUS_REFUND) {
            return apiResponse(false, "You can't change status anymore");
        }

        DB::beginTransaction();
        try {
            if ($order) {
                if ($request->status == Order::STATUS_REFUND && $order->isPaid()) {

                    $deposit = Deposit::create([
                        'uuid' => PaymentInvoice::generateUUID('DP-'),
                        'amount' => $order->gross_total,
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'last_four_digits' => 'credit from cancelation, refund ' . $order->warehouse_number,
                        'balance' => Deposit::getCurrentBalance($order->user) + $order->gross_total,
                        'is_credit' => true,
                    ]);

                    if ($order) {
                        $order->deposits()->sync($deposit->id);
                    }

                    $order->update([
                        'status'  => $request->status,
                        'is_paid' => false
                    ]);
                    DB::commit();
                    $this->sendTransactionMail($deposit, $preStatus, $user);
                    return apiResponse(true, "Updated");
                }

                if ($request->status == Order::STATUS_PAYMENT_DONE && !$order->is_paid) {
                    if(Deposit::getCurrentBalance($order->user) >= $order->gross_total) {
                        $deposit = Deposit::create([
                            'uuid' => PaymentInvoice::generateUUID('DP-'),
                            'amount' => $order->gross_total,
                            'user_id' => $order->user_id,
                            'order_id' => $order->id,
                            'last_four_digits' => 'admin Debit ' . $order->warehouse_number,
                            'balance' => Deposit::getCurrentBalance($order->user) - $order->gross_total,
                            'is_credit' => false,
                        ]);

                        if ($order) {
                            $order->deposits()->sync($deposit->id);
                        }
                        $this->sendTransactionMail($deposit, $preStatus, $user);
                        
                    } else {
                        return $this->rollback("Not Enough Balance. Please Add Balance to " . $order->user->name . ' ' . $order->user->pobox_number . " account.");
                    }

                }
                if ($order->isPaid()){
                    $order->update([
                        'status' => $request->status,
                    ]);
                    return $this->commit();
                }

                if(in_array($request->status, [
                    Order::STATUS_CANCEL, 
                    Order::STATUS_REJECTED, 
                    Order::STATUS_RELEASE, 
                    Order::STATUS_PAYMENT_PENDING, 
                    Order::STATUS_SHIPPED, 
                    ]))
                {
                    $order->update([
                        'status' => $request->status,
                        'is_paid' => $request->status >= Order::STATUS_PAYMENT_DONE ? true : false
                    ]);
                    //SendOrderMailNotification 
                    try {
                        \Mail::send(new OrderNotification($order, $preStatus, $user));
                    } catch (\Exception $ex) {
                        \Log::info('Notify Transaction email send error: ' . $ex->getMessage());
                    }
                }

                DB::commit();
                
                return apiResponse(true, "Updated");

            }
            return $this->rollback("Unhandle status selected");

        } catch (Exception $e) {
            return $this->rollback($e->getMessage());

        }
    }
    public function commit()
    {
        DB::commit();
        return apiResponse(true, "Updated");
    }

    public function rollback($message="Error while update")
    {
        DB::rollback();
        return apiResponse(false, $message);
    }

    private function sendTransactionMail($deposit, $preStatus, $user)
    {
        try {
            \Mail::send(new NotifyTransaction($deposit, $preStatus, $user));
        } catch (\Exception $ex) {
            \Log::info('Notify Transaction email send error: ' . $ex->getMessage());
        }
    }
}
