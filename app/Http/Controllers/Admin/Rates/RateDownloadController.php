<?php

namespace App\Http\Controllers\Admin\Rates;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\Reports\RateReportsRepository;
use App\Services\Excel\Export\ProfitSampleRateExport;
use App\Services\Excel\Export\ProfitPackageRateExport;
use App\Services\Excel\Export\ShippingServiceRegionRateExport;

class RateDownloadController extends Controller
{
    public function __invoke($packageId,$id,Request $request, RateReportsRepository $rateReportsRepository)
    {
        $service = ShippingService::find($id); 
        if(optional($service)->rates && $service->isGDEService()){
            if($service->rates && setting('gde', null, User::ROLE_ADMIN) && setting('gde', null, auth()->user()->id)){
                $profit = getGDEProfit($service->rates, $service->service_sub_class);
            }
            $exportService = new ShippingServiceRegionRateExport($service->rates, $profit); 
            return $exportService->handle(); 
        }
       
        if($service->is_brazil_redispatch){
            $rates = collect($service->rates->first()->data);
            $exportService = new ProfitPackageRateExport($rates); 
            return $exportService->handle();
        }
        
        $rateReportsRepository = new RateReportsRepository();
        $rates = $rateReportsRepository->getRateReport($packageId, $id);
        $exportService = new ProfitPackageRateExport($rates);
        return $exportService->handle();
    }
}
