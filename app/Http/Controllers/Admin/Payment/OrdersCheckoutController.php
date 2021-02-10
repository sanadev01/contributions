<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentInvoice;
use App\Repositories\OrderRepository;
use App\Services\PaymentServices\AuthorizeNetService;
use Illuminate\Http\Request;

class OrdersCheckoutController extends Controller
{
    public function index(PaymentInvoice $invoice)
    {
        $this->authorize('view',$invoice);

        if ( $invoice->isPaid() ){
            abort(404);
        }
        
        return view('admin.payment-invoices.checkout',compact('invoice'));
    }

    public function store(PaymentInvoice $invoice,Request $request, OrderRepository $orderRepository)
    {
        $this->authorize('view',$invoice);
        
        if ( $invoice->isPaid() ){
            abort(404);
        }
        
        if ( $orderRepository->checkout($request,$invoice) ){
            session()->flash('alert-success', __('orders.payment.alert-success'));
            return redirect()->route('admin.payment-invoices.index');
        }

        session()->flash('alert-danger',$orderRepository->getError());
        return \back()->withInput();

    }
}
