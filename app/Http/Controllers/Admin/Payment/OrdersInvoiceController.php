<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentInvoice;
use App\Repositories\PaymentInvoiceRepository;
use Illuminate\Http\Request;

class OrdersInvoiceController extends Controller
{

    public function show(PaymentInvoice $invoice)
    {
        $this->authorize('view',$invoice);

        return view('admin.payment-invoices.show',compact('invoice'));
    }

    public function edit(PaymentInvoice $invoice,PaymentInvoiceRepository $paymentInvoiceRepository)
    {
        $this->authorize('update',$invoice);

        $orders = $paymentInvoiceRepository->getUnpaidOrders();
        return view('admin.payment-invoices.edit',compact('invoice','orders'));
    }


    public function update(Request $request, PaymentInvoice $invoice, PaymentInvoiceRepository $paymentInvoiceRepository)
    {
        $this->authorize('update',$invoice);
        
        $this->validate($request, [
            'orders' => 'required|array|min:1',
            'orders.*' => 'required|integer|exists:orders,id'
        ],[
            'orders.*' => 'Please Select at leaset one Order to proceed',
            'orders.*.*' => 'Please select valid order. Order Id :input is invalid'
        ]);

        $invoice = $paymentInvoiceRepository->updateInvoice($request,$invoice);
        return redirect()->route('admin.payment-invoices.invoice.show',$invoice);
    }

}
