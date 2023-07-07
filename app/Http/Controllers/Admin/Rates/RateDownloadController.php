<?php

namespace App\Http\Controllers\Admin\Rates;

use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\Reports\RateReportsRepository;
use App\Services\Excel\Export\ProfitSampleRateExport;
use App\Services\Excel\Export\ProfitPackageRateExport;

class RateDownloadController extends Controller
{
    public function __invoke($packageId, $regionRates = null, Request $request, RateReportsRepository $rateReportsRepository)
    {
        if($request->service){
            $ServiceId = $request->service;
            $rates = $rateReportsRepository->getRateSample($ServiceId);
            if($rates){
                $exportService = new ProfitSampleRateExport($rates);
                return $exportService->handle();
            }

        }
        if($packageId == 0 ){
            $service = ShippingService::where('service_sub_class', ShippingService::Brazil_Redispatch)->first();
            $rates = collect($service->rates[0]->data);    
        }else{
            $rates = $rateReportsRepository->getRateReport($packageId);
        }
        
        if($packageId == 'gde'){
            $gdeRates = collect(json_decode($regionRates, true));
            $rates = $gdeRates;       
        }
        $exportService = new ProfitPackageRateExport($rates);
        return $exportService->handle();
    }
}
