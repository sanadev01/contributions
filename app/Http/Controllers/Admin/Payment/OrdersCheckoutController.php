<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentInvoice;
use App\Services\PaymentServices\AuthorizeNetService;
use Illuminate\Http\Request;

class OrdersCheckoutController extends Controller
{
    public function index(PaymentInvoice $invoice)
    {

    }

    public function store(PaymentInvoice $invoice,Request $request)
    {
        $authorizeNetService=  new AuthorizeNetService();
        
    }
}
