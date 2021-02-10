<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Repositories\PaymentInvoiceRepository;
use Illuminate\Http\Request;

class OrdersSelectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PaymentInvoice::class);
    }

    public function index(PaymentInvoiceRepository $paymentInvoiceRepository)
    {

        $orders = $paymentInvoiceRepository->getUnpaidOrders();

        return view('admin.payment-invoices.create',compact('orders'));
    }

    public function store(Request $request, PaymentInvoiceRepository $paymentInvoiceRepository)
    {
        $this->validate($request, [
            'orders' => 'required|array|min:1',
            'orders.*' => 'required|integer|exists:orders,id'
        ],[
            'orders.*' => 'Please Select at leaset one Order to proceed',
            'orders.*.*' => 'Please select valid order. Order Id :input is invalid'
        ]);

        $invoice = $paymentInvoiceRepository->createInvoice($request);
        return redirect()->route('admin.payment-invoices.invoice.show',$invoice);
    }
}
