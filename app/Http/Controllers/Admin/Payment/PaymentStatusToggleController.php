<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentInvoice;
use Illuminate\Http\Request;

class PaymentStatusToggleController extends Controller
{
    public function __invoke(PaymentInvoice $invoice)
    {
        $this->authorize('canChnageStatus',$invoice);

        $invoice->markPaid(
            !$invoice->isPaid()
        );
        session()->flash('alert-success','Status Updated');
        return back();
    }
}
