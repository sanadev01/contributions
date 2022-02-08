<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Order;
use App\Models\Deposit;
use App\Events\OrderPaid;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use PhpParser\Node\Stmt\Foreach_;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

        $stripeKey = null;

        $paymentGateway = setting('PAYMENT_GATEWAY', null, null, true);
        if($paymentGateway == 'STRIPE')
        {
            $stripeKey = setting('STRIPE_KEY', null, null, true);
        }
        
        return view('admin.payment-invoices.checkout',compact('invoice', 'paymentGateway', 'stripeKey'));
    }

    public function store(PaymentInvoice $invoice,Request $request, OrderRepository $orderRepository)
    {
        $this->authorize('view',$invoice);
        
        if ( $invoice->isPaid() ){
            abort(404);
        }

        if ($invoice->total_amount > $invoice->amount_paid) {
          return $this->handleUpdatedInvoice($invoice, $request, $orderRepository);
        }
    
        if($request->pay){

            if(getBalance() < $invoice->total_amount){
                session()->flash('alert-danger','Not Enough Balance. Please Recharge your account.');
                return back();
            }
            
            DB::beginTransaction();

            try {
                
                foreach($invoice->orders as $order){
                    if ( !$order->isPaid() &&  getBalance() >= $order->gross_total ){
                        chargeAmount($order->gross_total,$order);
                    }
                }
    
                $invoice->update([
                    'is_paid' => true
                ]);
    
                $invoice->orders()->update([
                    'is_paid' => true,
                    'status' => Order::STATUS_PAYMENT_DONE
                ]);

                DB::commit();
            } catch (\Exception $ex) {
                DB::rollBack();
                session()->flash('alert-danger',$ex->getMessage());
                return back();
            }
            
            
            event(new OrderPaid($invoice->orders, true));

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

    public function handleUpdatedInvoice($invoice, $request, $orderRepository)
    {
        
        if ($request->pay) {
           
            $amountToPay = $invoice->total_amount - $invoice->paid_amount;
            
            if(getBalance() < $amountToPay){
                session()->flash('alert-danger','Not Enough Balance. Please Recharge your account.');
                return back();
            }

            DB::transaction(function () use ($invoice, $amountToPay) {
                try {

                    $order = $invoice->orders->firstWhere('is_paid', false);
                    $description = 'Payment with invoice # '.$invoice->uuid.' for order # ';

                    chargeAmount($amountToPay,$order,$description);

                    $invoice->update([
                        'is_paid' => true,
                        'paid_amount' => $invoice->orders()->sum('gross_total'),
                    ]);

                    $invoice->orders()->update([
                        'is_paid' => true,
                        'status' => Order::STATUS_PAYMENT_DONE
                    ]);

                } catch (\Exception $ex) {
                    session()->flash('alert-danger',$ex->getMessage());
                    return back();
                }
            });
        }

        if(!$request->pay){
            if (!$orderRepository->checkout($request,$invoice) ){
                session()->flash('alert-danger',$orderRepository->getError());
                return back();
            }

            $invoice->update([
                'is_paid' => true,
                'paid_amount' => $invoice->orders()->sum('gross_total'),
            ]);

            $invoice->orders()->update([
                'is_paid' => true,
                'status' => Order::STATUS_PAYMENT_DONE
            ]);
        }


        event(new OrderPaid($invoice->orders, true));
        session()->flash('alert-success', __('orders.payment.alert-success'));
        return redirect()->route('admin.payment-invoices.index');
    }
}
