<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\PaymentInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentInvoiceRepository
{
    public function get()
    {
        $invoices = PaymentInvoice::query();

        if ( !Auth::user()->isAdmin() ){
            $invoices->where('paid_by',Auth::id());
        }

        return $invoices->paginate(50);
    }

    public function getUnpaidOrders()
    {
        $orders = Order::query()
                        ->where('user_id',Auth::id())
                        ->where('is_paid',false)
                        ->where('is_shipment_added',true)
                        ->where('status','>=',Order::STATUS_ORDER)
                        ->doesntHave('paymentInvoices');
        
        return $orders->get();
    }

    public function createInvoice(Request $request)
    {
        $invoice = PaymentInvoice::create([
            'uuid' => PaymentInvoice::generateUUID(),
            'paid_by' => Auth::id(),
            'order_count' => count($request->get('orders',[]))
        ]);

        $invoice->orders()->sync($request->get('orders',[]));

        $invoice->update([
            'total_amount' => $invoice->orders()->sum('gross_total')
        ]);

        return $invoice;
    }

    public function updateInvoice(Request $request,PaymentInvoice $invoice)
    {
        $invoice->orders()->sync($request->get('orders',[]));

        $invoice->update([
            'total_amount' => $invoice->orders()->sum('gross_total')
        ]);

        return $invoice;
    }
}
