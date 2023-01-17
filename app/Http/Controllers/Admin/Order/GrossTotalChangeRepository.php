<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Deposit;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\PaymentInvoice;
use Illuminate\Http\Request;

class GrossTotalChangeRepository {
    //this function will sync ( order and invoice ) if order status is STATUS_PAYMENT_DONE.
    public function updateInvoice(Order $newOrder,Order $oldOrder)
    {
 
        $difference = 0;

         $invoice = $newOrder->getPaymentInvoice(); 
            if($oldOrder->is_paid){

                $oldPaidAmount = $oldOrder->gross_total;
                $newPaidAmount = $newOrder->gross_total; 
                $difference = $newPaidAmount - $oldPaidAmount; 


                if ($difference<0) {
                    // the order amount descresed.
                    //so
                    //deposit/refund the difference to user account.
                    //remove refunded money from invoice.
                    
                    $this->deposite($newOrder, -$difference);
                    if ($invoice){
                        $invoice = $invoice->update([
                            'total_amount' => $invoice->total_amount + $difference,
                            'paid_amount' => $invoice->paid_amount + $difference
                        ]);
                    }
    
                }elseif ($difference > 0) {
                   // the amount increased.
                   //update invoice.for futher payment.
                    if ($invoice) {
                        $invoice->update([
                            'total_amount' => $invoice->total_amount + $difference,
                        ]);
                    } else {
                        //create new invoice if not exist. 
                        $invoice=  $this->createInvoice($newOrder,$difference,0); 
                    }
                   if ($invoice->total_amount > $invoice->paid_amount){
                        //if need to pay.
                        //then set order and invoice status unpaid. 
                        $this->setInvoiceUnpaid($invoice); 
                        $this->setOrderPending($newOrder);
                    }
                    else{
                        $this->setInvoicePaid($invoice); 
                        $this->setOrderDone($newOrder);
                    }
                }

            }
            elseif($oldOrder->status == Order::STATUS_PAYMENT_PENDING)
            {



                    if($invoice){
                        $oldPaidAmount =  $invoice->paid_amount;
                        $newPaidAmount = $invoice->orders()->sum('gross_total'); 
                        $difference = $newPaidAmount - $oldPaidAmount;
                    }
                    else{
                        $oldPaidAmount =  0;
                        $newPaidAmount = $newOrder->gross_total;
                        $difference = $newPaidAmount - $oldPaidAmount;
                    }

                    $oldOrderPaidAmount = $oldOrder->gross_total;
                    $newOrderPaidAmount = $newOrder->gross_total;
                    $orderDifference = $newOrderPaidAmount - $oldOrderPaidAmount;


                    // dump('difference');
                    // dump($difference);

                    // dump('order difference');
                    // dd($orderDifference);

                     if($orderDifference==0){
                        return true;
                     }


                    if ($difference<0){
                        // the order amount descresed.
                        //so
                        //deposit/refund the difference to user account.
                        //remove refunded money from invoice.
                    
                        $this->deposite($newOrder, -$difference);
                        if ($invoice){
                            $this->setInvoicePaid($invoice);
                            $this->setOrderDone($newOrder);
                        }
        
                    }elseif($difference > 0){
                       //1. The amount increased.
                       //2. Update invoice.for futher payment.
                        if ($invoice) {
                            $invoice->update([
                                'total_amount' => $invoice->total_amount + $orderDifference,
                            ]);
                        }else{
                            //create new invoice if not exist.
                            $invoice=  $this->createInvoice($newOrder,$difference,0); 
                        }

                       if ($invoice->total_amount > $invoice->paid_amount){
                            //if need to pay.
                            //then set order and invoice status unpaid. 
                            $this->setInvoiceUnpaid($invoice); 
                            $this->setOrderPending($newOrder);
                        }
                        else{
                            $this->setInvoicePaid($invoice); 
                            $this->setOrderDone($newOrder);
                        }
                    }

                   else{
                        //back to paid status.without any payment change.
                        $this->setInvoicePaid($invoice); 
                        $this->setOrderDone($newOrder);
                    }



            }
            else{
                return true;
            }
             
            //refund amount 
                
            return true;
 
    }
 
    public function setInvoicePaid($invoice)
    {
        $invoice->update([
            'is_paid' => 1,
            'total_amount' => $invoice->total_amount ,
            'paid_amount' => $invoice->total_amount
        ]);
    }

    public function setInvoiceUnpaid($invoice)
    {
        $invoice->update([
            'is_paid' => 0,
        ]);
    }

    public function setOrderPending($newOrder)
    {
        $newOrder->update([
            'status' => Order::STATUS_PAYMENT_PENDING,
            'is_paid' => 0,
        ]);
        OrderTracking::where('order_id', $newOrder->id)->update(['status_code' => Order::STATUS_PAYMENT_PENDING]);
    }

    public function setOrderDone($newOrder)
    {
        $newOrder->update([
            'status' => Order::STATUS_PAYMENT_DONE,
            'is_paid' => 1,
        ]);
        OrderTracking::where('order_id', $newOrder->id)->update(['status_code' => Order::STATUS_PAYMENT_DONE]);
    }
    public function deposite($newOrder, $difference)
    {
        if($difference!=0)
        Deposit::create([
            'uuid' => PaymentInvoice::generateUUID('DP-'),
            'amount' => $difference,
            'user_id' => $newOrder->user->id,
            'order_id' => $newOrder->id,
            'balance' => Deposit::getCurrentBalance($newOrder->user) + $difference,
            'is_credit' => true,
            'description' => "Change of Order Amount",
        ]);
    }
    public function createInvoice($newOrder,$totalAmount,$paidAmount)
    { 
        $invoice =  PaymentInvoice::create([
            'uuid' => PaymentInvoice::generateUUID(),
            'paid_by' => $newOrder->user->id,
            'order_count' => '1',
            'total_amount' => $totalAmount,
            'is_paid'     => 0,
            'paid_amount' => $paidAmount,
            'type' => auth()->user()->can('canCreatePostPaidInvoices', PaymentInvoice::class) ? PaymentInvoice::TYPE_POSTPAID : PaymentInvoice::TYPE_PREPAID
        ]);
        
        $invoice->orders()->sync($newOrder->id); 
        return $invoice;
    }
}