<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Order;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use PhpParser\Node\Stmt\Foreach_;
use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use App\Services\PaymentServices\AuthorizeNetService;

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

        if($request->pay){

            if(getBalance() < $invoice->total_amount){
                session()->flash('alert-danger','Not Enough Balance. Please Recharge your account.');
                return back();
            }
            
            // foreach($invoice->orders as $order){
            //     if ( !$order->isPaid() &&  getBalance() >= $order->gross_total ){
            //         chargeAmount($order->gross_total,$order);
            //     }
            // }
            chargeAmount($invoice->total_amount);
            $invoice->update([
                'is_paid' => true
            ]);

            $invoice->orders()->update([
                'is_paid' => true,
                'status' => Order::STATUS_PAYMENT_DONE
            ]);

            session()->flash('alert-success', __('orders.payment.alert-success'));
            return redirect()->route('admin.payment-invoices.index');
        }

        if ( $orderRepository->checkout($request,$invoice) ){
            session()->flash('alert-success', __('orders.payment.alert-success'));
            return redirect()->route('admin.payment-invoices.index');
        }

        session()->flash('alert-danger',$orderRepository->getError());
        return \back()->withInput();

    }
}
