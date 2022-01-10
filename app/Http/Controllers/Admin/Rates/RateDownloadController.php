<?php

namespace App\Http\Controllers\Admin\Rates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Reports\RateReportsRepository;
use App\Services\Excel\Export\ProfitPackageRateExport;
use App\Services\Excel\Export\ProfitSampleRateExport;

class RateDownloadController extends Controller
{
    public function __invoke($packageId,Request $request, RateReportsRepository $rateReportsRepository)
    {
        if($request->service){
            $ServiceId = $request->service;
            $rates = $rateReportsRepository->getRateSample($ServiceId);
            if($rates){
                $exportService = new ProfitSampleRateExport($rates);
                return $exportService->handle();
            }

        }
        $rates = $rateReportsRepository->getRateReport($packageId);
        // dd($rates);
        $exportService = new ProfitPackageRateExport($rates);
        return $exportService->handle();
    }
}
