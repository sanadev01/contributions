<?php

namespace App\Http\Controllers\Admin\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Excel\Export\SaleExport;
use App\Repositories\AffiliateSaleRepository;

class SaleExportController extends Controller
{
    public function __invoke(Request $request, AffiliateSaleRepository $affiliateSaleRepository)
    {
        // $sales = $affiliateSaleRepository->getSalesForExport($request);
        $sales = $affiliateSaleRepository->get($request, false);

        $exportService = new SaleExport($sales->sortByDesc('order.user_id'));
        return $exportService->handle();
    }
}
