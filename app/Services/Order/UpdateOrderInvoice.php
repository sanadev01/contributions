<?php
namespace App\Services\Order;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\PaymentInvoice;
class UpdateOrderInvoice
{
    //this function will sync ( order and invoice ) if order status is STATUS_PAYMENT_DONE.
    public function update(Order $newOrder, Order $oldOrder)
    {

        $paidDifference = 0;

        $invoice = $newOrder->getPaymentInvoice();
        //ref.on paid change.
        if ($oldOrder->is_paid) {

            $oldPaidAmount = $oldOrder->gross_total;
            $newPaidAmount = $newOrder->gross_total;
            $paidDifference = $newPaidAmount - $oldPaidAmount;

            if ($paidDifference < 0) {
                // the order amount descresed.
                //so
                //deposit/refund the difference to user account.
                //remove refunded money from invoice.

                $this->deposite($newOrder, -$paidDifference);
                if ($invoice) {
                    $invoice = $invoice->update([
                        'total_amount' => $invoice->total_amount + $paidDifference,
                        'paid_amount' => $invoice->paid_amount + $paidDifference
                    ]);
                }
            } elseif ($paidDifference > 0) {
                // the amount increased.
                //update invoice.for futher payment. 
                if ($invoice) {
                    $invoice->update([
                        'total_amount' => $invoice->total_amount + $paidDifference,
                    ]);
                } else {
                    //create new invoice if not exist. 
                    $invoice =  $this->createInvoice($newOrder, $newOrder->gross_total, $oldOrder->gross_total);
                }
                if ($invoice->total_amount > $invoice->paid_amount) {
                    //if need to pay.
                    //then set order and invoice status unpaid. 
                    $this->setInvoiceUnpaid($invoice);
                    $this->setOrderPending($newOrder);
                } else {

                    $this->setInvoicePaid($invoice);
                    $this->setOrderDone($newOrder);
                }
            }
        } elseif ($oldOrder->status == Order::STATUS_PAYMENT_PENDING) {



            if ($invoice) {
                $oldPaidAmount =  $invoice->paid_amount;
                $newPaidAmount = $invoice->orders()->sum('gross_total');
                $paidDifference = $newPaidAmount - $oldPaidAmount;
            } else {
                //unable to change multiple times without invoice.
                // this is for caution. we dont expect this condition.becuse we are aleady creating for order in (ref.on paid change).
                return false;
            }
            $oldOrderPaidAmount = $oldOrder->gross_total;
            $newOrderPaidAmount = $newOrder->gross_total;
            $orderDifference = $newOrderPaidAmount - $oldOrderPaidAmount;
            if ($orderDifference == 0) {
                //no change made.
                return true;
            }
            if ($paidDifference < 0) {
                // the order amount descresed.
                //so
                //deposit/refund the difference to user account.
                //remove refunded money from invoice.
                $this->deposite($newOrder, -$paidDifference);

                if ($invoice) {
                    $invoice->update([
                        'total_amount' => $invoice->paid_amount + $paidDifference,
                    ]);
                    $this->setInvoicePaid($invoice);
                    $this->setOrderDone($newOrder);
                }
            } elseif ($paidDifference > 0) {
                //1. The amount increased.
                //2. Update invoice.for futher payment.
                if ($invoice) {
                        $invoice->update([
                            'total_amount' => $invoice->total_amount + $orderDifference,
                        ]);
                } else {
                    //create new invoice if not exist.
                    $invoice =  $this->createInvoice($newOrder, $paidDifference, 0);
                }

                if ($invoice->total_amount > $invoice->paid_amount) {
                    //if need to pay.
                    //then set order and invoice status unpaid. 
                    $this->setInvoiceUnpaid($invoice);
                    $this->setOrderPending($newOrder);
                } else {
                    $this->setInvoicePaid($invoice);
                    $this->setOrderDone($newOrder);
                }
            } else {
                //back to paid status.without any payment change.
                    $invoice->update([
                        'total_amount' => $invoice->total_amount + $orderDifference,
                    ]);
                $this->setInvoicePaid($invoice);
                $this->setOrderDone($newOrder);
            }
        } else {
            return true;
        }
        return true;
    }

    public function setInvoicePaid($invoice)
    {
        $invoice->update([
            'is_paid' => 1,
            'total_amount' => $invoice->total_amount,
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
        if ($difference != 0)
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
    public function createInvoice($newOrder, $totalAmount, $paidAmount)
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
