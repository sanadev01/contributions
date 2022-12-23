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
            $grossTotal = $order->gross_total;
            $paidAmount = 0;
            if($invoice){
             $invoicePaidAmount = $invoice->orders()->sum('gross_total');

            }
            else{

            }
            $paidAmount = $order->gross_total;
            $totalAmount = $request->user_declared_freight;

            $pendingAmount = $totalAmount - $paidAmount;
            
        dump($request->all());
        dump($order);
        dump('paidAmount',$paidAmount);
        dump('totalAmount',$totalAmount);
        dump('pendingAmount',$pendingAmount);
        dump('isPaid',$order->isPaid());
        dump('STATUS_PAYMENT_PENDING', $order->status == Order::STATUS_PAYMENT_PENDING);  
        dd('pending branch');
      
        if ($order->isPaid() || $order->status == Order::STATUS_PAYMENT_PENDING) {
            //refund amount 

            if ($pendingAmount<0) {
                // the order amount descresed.
                //so
                //deposit/refund the difference to user account.
                //remove refunded money from invoice.
                if($order->isPaid())
                $this->changeOfOrderDeposit($order, -$pendingAmount);

                if ($invoice){
                    $invoice = $invoice->update([
                        'total_amount' => $invoice->total_amount - $pendingAmount,
                        'paid_amount' => $invoice->paid_amount + $pendingAmount
                    ]);
                }

            } elseif ($pendingAmount > 0) {
               // the amount increased.
               //update invoice.for futher payment.  

                if ($invoice) {
                    $invoice->update([
                        'total_amount' => $invoice->total_amount + $pendingAmount,
                    ]);
                } else {

                    //create new invoice if not exist. 
                    $invoice=  $this->createInvoice($order,$totalAmount,$paidAmount); 
                }      
                
                if ($invoice->total_amount > $invoice->paid_amount) { 
                    //if need to pay.
                    //then set order and invoice status unpaid.
                    $this->setInvoiceUnpaid($invoice); 
                    $this->setOrderPending($order);
                }
                else{
                    
                    $this->setInvoicePaid($invoice); 
                    $this->setOrderDone($order);
                }  
            }

            // return dd($invoice);


        }
        // else{
        //     $this->changesOnPending($order,$request);
        // }
    }

    //this function will sync ( order and invoice ) if order status is STATUS_PAYMENT_PENDING.
    // public function changesOnPending(Order $order,$request)
    // {
    //     $invoice = $order->getPaymentInvoice();  

    //     if ($order->status == Order::STATUS_PAYMENT_PENDING) {

    //     $paidAmount = $order->gross_total;
    //     $currentAmount = $request->user_declared_freight;




















    //         if ($invoice) {
    //             $invoice->update([
    //                 'total_amount' => $order->orders()->sum('gross_total'),
    //             ]);
    //         }
    //         else{
    //             $debit = $order->deposits()->where('is_credit',0)->sum('amount');
    //             $credit = $order->deposits()->where('is_credit',1)->sum('amount');
    //             $paidWithoutInvoice = $debit-$credit;
    //             $invoice = $this->createInvoice($order,$order->gross_total,$paidWithoutInvoice);
                
    //         }
  
    //         if ($invoice->total_amount > $invoice->paid_amount) {  
    //             // set order and invoice unpaid
    //             $this->setInvoiceUnpaid($invoice);
    //             $this->setOrderPending($order); 

    //         } elseif ($invoice->total_amount < $invoice->paid_amount) {
    //             // set invoice and order paid. and deposite extra amount.
                
    //             $difference = $invoice->paid_amount - $invoice->total_amount;

    //             //deposite extra amount to order user.
    //             $this->changeOfOrderDeposit($order, $difference);
    //             //remove the deposite amount. from invoice
    //             $invoice->update([
    //                 'paid_amount' => $invoice->paid_amount - $difference,
    //                 'is_paid' => 1,
    //             ]);

    //             //set order paid
    //             $this->setOrderDone($order); 

    //         } else { 
    //             // if invoice->total_amount equal to $invoice->paid_amount
    //             //sent the order and invoice paid.
    //             $this->setInvoicePaid($invoice);
    //             $this->setOrderDone($order);
    //         }

    //     }
    // }

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
        $invoice =  PaymentInvoice::create([
            'uuid' => PaymentInvoice::generateUUID(),
            'paid_by' => $order->user->id,
            'order_count' => '1',
            'total_amount' => $totalAmount,
            'is_paid'     => 0,
            'paid_amount' => $paidAmount,
            'type' => auth()->user()->can('canCreatePostPaidInvoices', PaymentInvoice::class) ? PaymentInvoice::TYPE_POSTPAID : PaymentInvoice::TYPE_PREPAID
        ]);
        
        $invoice->orders()->sync($order->id); 
        return $invoice;
    }
}