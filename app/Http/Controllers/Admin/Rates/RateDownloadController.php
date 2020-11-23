<?php

namespace App\Http\Controllers\Admin\Rates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Reports\RateReportsRepository;
use App\Services\Excel\Export\ProfitPackageRateExport;
class RateDownloadController extends Controller
{
    public function __invoke($packageId, RateReportsRepository $rateReportsRepository)
    {
        $rates = $rateReportsRepository->getRateReport($packageId);
        dd($rates);
        $exportService = new ProfitPackageRateExport($rates);
        return $exportService->handle();
    }
}
