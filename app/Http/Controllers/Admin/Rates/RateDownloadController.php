<?php

namespace App\Http\Controllers\Admin\Rates;

use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Models\Rate;
use App\Repositories\Reports\RateReportsRepository;
use App\Services\Excel\Export\ProfitSampleRateExport;
use App\Services\Excel\Export\ProfitPackageRateExport;
use App\Services\Excel\Export\ShippingServiceRegionRateExport;

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
                
        if($packageId == 'gde'){
            $service = ShippingService::where('service_sub_class', ShippingService::GDE_PRIORITY_MAIL)->first();
            
        $rates = Rate::where([
            ['shipping_service_id', $service->id],
            ['region_id', '!=', null]
        ])->get(); 

        $exportService = new ShippingServiceRegionRateExport($rates);
        return $exportService->handle(); 
        }
        if($packageId == 0 ){
            $service = ShippingService::where('service_sub_class', ShippingService::Brazil_Redispatch)->first();
            $rates = collect($service->rates[0]->data);    
        }else{
            $rates = $rateReportsRepository->getRateReport($packageId);
        }

        
        $exportService = new ProfitPackageRateExport($rates);
        return $exportService->handle();
    }
}
