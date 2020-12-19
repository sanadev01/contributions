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
            $query->where('paid_by',Auth::id());
        }

        if ( $request->user ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('pobox_number',"%{$request->user}%")
                            ->orWhere('name','LIKE',"%{$request->user}%")
                            ->orWhere('last_name','LIKE',"%{$request->user}%")
                            ->orWhere('email','LIKE',"%{$request->user}%");
            });
        }

        if ( $request->type ){
            $query->where('type',$request->type);
        }

        if ( $request->is_paid !=null ){
            $query->where('is_paid',$request->is_paid);
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
                        ->where('gross_total','>',0)
                        ->where('shipping_service_id','!=',null)
                        ->doesntHave('paymentInvoices');
        
        return $orders->get();
    }

    public function createInvoice(Request $request)
    {
        $orders = Order::find($request->get('orders',[]));

        $orders = collect($orders->filter(function($order){
            return !$order->getPaymentInvoice();
        })->all());

        $invoice = PaymentInvoice::create([
            'uuid' => PaymentInvoice::generateUUID(),
            'paid_by' => Auth::id(),
            'order_count' => $orders->count(),
            'type' => auth()->user()->can('canCreatePostPaidInvoices', PaymentInvoice::class) ? PaymentInvoice::TYPE_POSTPAID : PaymentInvoice::TYPE_PREPAID
        ]);


        $invoice->orders()->sync($orders->pluck('id')->toArray());

        $invoice->update([
            'total_amount' => $invoice->orders()->sum('gross_total')
        ]);

        return $invoice;
    }

    public function updateInvoice(Request $request,PaymentInvoice $invoice)
    {
        $orders = Order::find($request->get('orders',[]));
        // dd($orders);

        $orders = collect($orders->filter(function($order) use($invoice){
            return !$order->getPaymentInvoice() || $order->getPaymentInvoice()->id === $invoice->id;
        })->all());

        $invoice->orders()->sync($orders->pluck('id')->toArray());

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

    public function getPaymentInvoiceForExport($request)
    {
        $query = PaymentInvoice::query();

        if ( !Auth::user()->isAdmin() ){
            $query->where('paid_by',Auth::id());
        }
        
        if ( $request->start_date ){
            $query->where('created_at','>',$request->start_date);
        }
        
        if ( $request->end_date ){
            $query->where('created_at','<=',$request->end_date);
        }
        
        return $query->get();
    }
}
