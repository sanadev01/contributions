<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Excel\Export\PaymentInvoiceExport;
use App\Repositories\PaymentInvoiceRepository;

class PaymentInvoiceExportController extends Controller
{
    public function __invoke(Request $request, PaymentInvoiceRepository $paymentInvoiceRepository)
    {
        $paymentInvoices = $paymentInvoiceRepository->getPaymentInvoiceForExport($request);
        
        $exportService = new PaymentInvoiceExport($paymentInvoices);
        return $exportService->handle();
    }
}
