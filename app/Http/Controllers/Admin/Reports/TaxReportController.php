<?php

namespace App\Http\Controllers\Admin\Reports;

use Illuminate\Http\Request;
use App\Repositories\TaxRepository;
use App\Http\Controllers\Controller;
use App\Services\Excel\Export\ExportTax;

class TaxReportController extends Controller
{
    function __invoke(TaxRepository $repository, Request $request)
    {
        $taxes = $repository->get($request, false);
        $taxExportService = new ExportTax($taxes);
        return $taxExportService->handle();
    }
}
