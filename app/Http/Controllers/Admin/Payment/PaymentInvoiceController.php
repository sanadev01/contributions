<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentInvoice;
use App\Repositories\PaymentInvoiceRepository;
class PaymentInvoiceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PaymentInvoice::class);    
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view_payment_invoice',PaymentInvoice::class);
        return view('admin.payment-invoices.index');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentInvoice $paymentInvoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentInvoice $paymentInvoice, PaymentInvoiceRepository $paymentInvoiceRepository)
    {
        if ( $paymentInvoiceRepository->delete($paymentInvoice) ){
            session()->flash('alert-success','Payment Invoice Deleted');
            return back();
        }

        session()->flash('alert-danger','Error While deleting Payment Invoice');
        return back();
    }
}
