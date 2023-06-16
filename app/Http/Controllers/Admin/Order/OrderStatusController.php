<?php

namespace App\Http\Controllers\Admin\Order;

use Exception;
use App\Models\Order;
use App\Models\Deposit;
use App\Events\OrderPaid;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\Admin\NotifyTransaction;
use App\Mail\Admin\OrderNotification;
use Illuminate\Database\Eloquent\Collection;

class OrderStatusController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!$request->user()->isAdmin())
            return apiResponse(false, "Only admin can change the order status");

        $order = Order::find($request->order_id);
        $user = $request->user;
        $preStatus = $order->status_name;
        $orders = new Collection;

        if ($order->status == Order::STATUS_REFUND) {
            return apiResponse(false, "You can't change status anymore");
        } 
        if ( $order->status == Order::STATUS_SHIPPED) {
            return apiResponse(false, "Order is already shipped");
        } 
        
        if ( !$order->isPaid() && $request->status == Order::STATUS_SHIPPED) {
            return apiResponse(false, "You can't change order status unless to pay");
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
                    optional($order->affiliateSale)->delete();
                    DB::commit();
                    $this->sendTransactionMail($deposit, $preStatus, $user);
                    return apiResponse(true, "Order has been Refunded");
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

                        $order->deposits()->sync($deposit->id);

                        $order->update([
                            'status'  => $request->status,
                            'is_paid' => true
                        ]);

                        $orders->push($order);
                        event(new OrderPaid($orders, true));
                        $this->sendTransactionMail($deposit, $preStatus, $user);

                        return $this->commit();

                    } else {
                        return $this->rollback("Not Enough Balance. Please Add Balance to " . $order->user->name . ' ' . $order->user->pobox_number . " account.");
                    }

                }
                if ($order->isPaid() && $request->status == Order::STATUS_SHIPPED){
                    $order->update([
                        'status' => $request->status,
                    ]);
                    return $this->commit();
                }
                // optional($order->affiliateSale)->delete();
                if(in_array($request->status, [
                    Order::STATUS_CANCEL, 
                    Order::STATUS_REJECTED, 
                    Order::STATUS_RELEASE, 
                    Order::STATUS_PAYMENT_PENDING,
                ])){
                    $order->update([
                        'status' => $request->status,
                        'is_paid' => false
                    ]); 
                    //SendOrderMailNotification 
                    try {
                        \Mail::send(new OrderNotification($order, $preStatus, $user));
                    } catch (\Exception $ex) {
                        \Log::info('Order notification email send error: ' . $ex->getMessage());
                    }
                }
                return $this->commit();

            }
            return $this->rollback("Unhandle status selected");

        } catch (Exception $e) {
            return $this->rollback($e->getMessage());

        }
    }
    public function commit()
    {
        DB::commit();
        return apiResponse(true, "Order Status has been changed");
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
            \Log::info('Order status Notify Transaction email send error: ' . $ex->getMessage());
        }
    }
}
