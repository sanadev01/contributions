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
        return view('admin.payment-invoices.checkout',compact('invoice'));
    }

    public function store(PaymentInvoice $invoice,Request $request, OrderRepository $orderRepository)
    {
        if ( $orderRepository->checkout($request,$invoice) ){
            session()->flash('alert-success', __('orders.payment.alert-success'));
            return redirect()->route('admin.payment-invoices.index');
        }

        session()->flash('alert-danger',$orderRepository->getError());
        return \back()->withInput();

    }
}
