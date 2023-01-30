<?php

namespace App\Http\Controllers\Admin\Payment;

use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use PhpParser\Node\Stmt\Foreach_;
use App\Http\Controllers\Controller;
use App\Repositories\OrderCheckoutRepository;
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

    public function store(PaymentInvoice $invoice,Request $request, OrderCheckoutRepository $orderCheckoutRepository)
    { 
        $this->authorize('view',$invoice);
        if ( $invoice->isPaid() ){
            abort(404);
        }

        $request->merge(['payment_gateway' => 'authorize']);
        
        return $orderCheckoutRepository->handle($invoice, $request);
    }
}
