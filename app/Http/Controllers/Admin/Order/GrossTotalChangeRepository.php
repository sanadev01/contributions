<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Deposit;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\PaymentInvoice;
use Illuminate\Http\Request;

class GrossTotalChangeRepository {
    //this function will sync ( order and invoice ) if order status is STATUS_PAYMENT_DONE.
    public function changesOnPaid(Request $request, Order $order)
    {
        $invoice = $order->getPaymentInvoice();
        if ($order->isPaid()) {
            //refund amount 
            $paidAmount = $order->gross_total;
            $currentAmount = $request->user_declared_freight;
            if ($paidAmount > $currentAmount) {
                // the order amount descresed.
                //so
                //deposit/refund the difference to user account.
                //remove refunded money from invoice.
                $difference = $paidAmount - $currentAmount;
                $this->changeOfOrderDeposit($order, $difference);
                if ($invoice) {
                    $invoice->update([
                        'total_amount' => $invoice->orders()->sum('gross_total'),
                        'paid_amount' => $invoice->paid_amount - $difference
                    ]);
                }
            } elseif ($currentAmount > $paidAmount) {
               // the amount increased.
               //update invoice.for futher payment. 

                if ($invoice) {
                    $invoice->update([
                        'total_amount' => $invoice->orders()->sum('gross_total'),
                    ]);
                } else {
                    //create new invoice if not exist.
                    $invoice=  $this->createInvoice($order,$currentAmount,$paidAmount);
  
                } 

                if ($invoice->total_amount > $invoice->paid_amount) { 
                 //if need to pay.
                //then set order and invoice status unpaid.
                $this->setInvoiceUnpaid($invoice); 
                $this->setOrderPending($order);
            }
            }


        }
    }

    //this function will sync ( order and invoice ) if order status is STATUS_PAYMENT_PENDING.
    public function changesOnPending(Order $order)
    {
        $invoice = $order->getPaymentInvoice();  
        
        if ($order->status == Order::STATUS_PAYMENT_PENDING) {
            if ($invoice) {
                $invoice->update([
                    'total_amount' => $invoice->orders()->sum('gross_total'),
                ]);
            }
            else{
                $debit = $order->deposits()->where('is_credit',0)->sum('amount');
                $credit = $order->deposits()->where('is_credit',1)->sum('amount');
                $paidWithoutInvoice = $debit-$credit;
                $invoice = $this->createInvoice($order,$order->gross_total,$paidWithoutInvoice);
                
            }
  
            if ($invoice->total_amount > $invoice->paid_amount) {  
                // set order and invoice unpaid
                $this->setInvoiceUnpaid($invoice);
                $this->setOrderPending($order); 

            } elseif ($invoice->total_amount < $invoice->paid_amount) {
                // set invoice and order paid. and deposite extra amount.
                
                $difference = $invoice->paid_amount - $invoice->total_amount;

                //deposite extra amount to order user.
                $this->changeOfOrderDeposit($order, $difference);
                //remove the deposite amount. from invoice
                $invoice->update([
                    'paid_amount' => $invoice->paid_amount - $difference,
                    'is_paid' => 1,
                ]);
                //set order paid
                $this->setOrderDone($order); 

            } else { 
                // if invoice->total_amount equal to $invoice->paid_amount
                //sent the order and invoice paid.
                $this->setInvoicePaid($invoice);
                $this->setOrderDone($order);
            }

        }
    }

    public function setInvoicePaid($invoice)
    {
        $invoice->update([
            'is_paid' => 1,
        ]);
    }

    public function setInvoiceUnpaid($invoice)
    {
        $invoice->update([
            'is_paid' => 0,
        ]);
    }

    public function setOrderPending($order)
    {
        $order->update([
            'status' => Order::STATUS_PAYMENT_PENDING,
            'is_paid' => 0,
        ]);
        OrderTracking::where('order_id', $order->id)->update(['status_code' => Order::STATUS_PAYMENT_PENDING]);
    }

    public function setOrderDone($order)
    {
        $order->update([
            'status' => Order::STATUS_PAYMENT_DONE,
            'is_paid' => 1,
        ]);
        OrderTracking::where('order_id', $order->id)->update(['status_code' => Order::STATUS_PAYMENT_DONE]);
    }
    public function changeOfOrderDeposit($order, $difference)
    {
        Deposit::create([
            'uuid' => PaymentInvoice::generateUUID('DP-'),
            'amount' => $difference,
            'user_id' => $order->user->id,
            'order_id' => $order->id,
            'balance' => Deposit::getCurrentBalance($order->user) + $difference,
            'is_credit' => true,
            'description' => "Change of Order Amount",
        ]);
    }
    public function createInvoice($order,$totalAmount,$paidAmount)
    {
        return PaymentInvoice::create([
            'uuid' => PaymentInvoice::generateUUID(),
            'paid_by' => $order->user->id,
            'order_count' => '1',
            'total_amount' => $totalAmount,
            'is_paid'     => 0,
            'paid_amount' => $paidAmount,
            'type' => auth()->user()->can('canCreatePostPaidInvoices', PaymentInvoice::class) ? PaymentInvoice::TYPE_POSTPAID : PaymentInvoice::TYPE_PREPAID
        ]);
    }
}