<?php

namespace App\Http\Controllers\Admin\Rates;

use App\Http\Controllers\Controller;
use App\Models\ShippingService;
use Illuminate\Http\Request;
use App\Repositories\Reports\RateReportsRepository;
use App\Services\Excel\Export\ProfitPackageRateExport;
class RateDownloadController extends Controller
{
    public function __invoke($packageId,Request $request, RateReportsRepository $rateReportsRepository)
    {
        if($request->service){
            $ServiceId = $request->service;
            $rates = $rateReportsRepository->getRateSample($ServiceId);
            $exportService = new ProfitPackageRateExport($rates);
            return $exportService->handle();
        }
        $rates = $rateReportsRepository->getRateReport($packageId);
        $exportService = new ProfitPackageRateExport($rates);
        return $exportService->handle();
    }
}
