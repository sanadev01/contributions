<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentInvoice;
use Illuminate\Http\Request;

class PaymentTypeToggleController extends Controller
{
    public function __invoke(PaymentInvoice $invoice)
    {
        $this->authorize('canChnageType',$invoice);

        if ( $invoice->isPrePaid() ){
            $invoice->markPostPaid();
        }
        else if ( !$invoice->isPrePaid() ){
            $invoice->markPrePaid();
        }

        session()->flash('alert-success','Status Updated');
        return back();
    }
}
