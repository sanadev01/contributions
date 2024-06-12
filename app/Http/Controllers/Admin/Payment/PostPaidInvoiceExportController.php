<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentInvoice;
use App\Services\Excel\Export\ExportPostPaidInvoice;
use Illuminate\Http\Request;

class PostPaidInvoiceExportController extends Controller
{
    public function __invoke(PaymentInvoice $invoice)
    {
        $this->authorize('view',$invoice);
        
        $exportService = new ExportPostPaidInvoice($invoice);
        return $exportService->handle();
    }
}
