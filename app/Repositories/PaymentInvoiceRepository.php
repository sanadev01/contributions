<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\PaymentInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentInvoiceRepository
{
    public function get(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='asc')
    {
        $query = PaymentInvoice::query();

        if ( !Auth::user()->isAdmin() ){
            $invoices->where('paid_by',Auth::id());
        }

        if ( $request->user ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('pobox_number',"%{$request->user}%")
                            ->orWhere('name','LIKE',"%{$request->user}%")
                            ->orWhere('last_name','LIKE',"%{$request->user}%")
                            ->orWhere('email','LIKE',"%{$request->user}%");
            });
        }

        if ( $request->uuid ){
            $query->where('uuid','LIKE',"%{$request->uuid}%");
        }

        if ( $request->last_four_digits ){
            $query->where('last_four_digits','LIKE',"%{$request->last_four_digits}%");
        }

        $query->orderBy($orderBy,$orderType);

        return $paginate ? $query->paginate($pageSize) : $query->get();
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
            'total_amount' => $invoice->orders()->sum('gross_total'),
            'order_count' => $invoice->orders()->count()
        ]);

        return $invoice;
    }

    public function delete(PaymentInvoice  $paymentInvoice)
    {
        $paymentInvoice->orders()->sync([]);
        $paymentInvoice->delete();

        return true;
    }
}
