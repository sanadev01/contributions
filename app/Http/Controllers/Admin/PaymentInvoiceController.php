<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentInvoice;
use App\Repositories\PaymentInvoiceRepository;
use Illuminate\Http\Request;

class PaymentInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PaymentInvoiceRepository $paymentInvoiceRepository)
    {
        $invoices = $paymentInvoiceRepository->get();
        return view('admin.payment-invoices.index',compact('invoices'));
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
    public function destroy(PaymentInvoice $paymentInvoice)
    {
        //
    }
}
