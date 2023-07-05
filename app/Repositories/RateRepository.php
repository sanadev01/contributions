<?php

namespace App\Repositories;

use Exception;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use App\Services\Excel\ImportCharges\ImportRates;
use App\Services\Excel\ImportCharges\ImportCourierExpressRates;
use App\Services\Excel\ImportCharges\ImportPostNLRates;
use App\Services\Excel\ImportCharges\ImportGDERates;

class RateRepository
{
    public function get()
    {   
        $rates = Rate::has('shippingService')->get()->sortBy('shippingService.name');

        return $rates->unique('shipping_service_id');
    }

    public function store(Request $request)
    {   
        try{

            $file = $request->file('csv_file');
            $shippingService = ShippingService::where('id', $request->shipping_service_id)->first();
            
            try {
                if ($shippingService && $shippingService->service_sub_class == ShippingService::Courier_Express) {
                    $importCourierExpressService = new ImportCourierExpressRates($file, $shippingService, $request);
                    $importCourierExpressService->handle();
                }elseif ($shippingService && $shippingService->service_sub_class == ShippingService::PostNL) {
                    $importPostNLRates = new ImportPostNLRates($file, $shippingService, $request);
                    $importPostNLRates->handle();
                }elseif ($shippingService && ($shippingService->service_sub_class == ShippingService::GDE_PRIORITY_MAIL || $shippingService->service_sub_class == ShippingService::GDE_FIRST_CLASS)) {
                    $importPostNLRates = new ImportGDERates($file, $shippingService, $request);
                    $importPostNLRates->handle();
                }else
                {
                    $importService = new ImportRates($file, $shippingService, $request->country_id);
                    $importService->handle();
                }
                
                session()->flash('alert-success', 'shipping-rates.Rates Updated Successfully');
                return true;

            } catch (\Exception $exception) {
                throw $exception;
                session()->flash('alert-danger', 'shipping-rates.Error While Updating Rates');
                return back();
            }

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Rate: '.$exception->getMessage());
            return null;
        }
    }

    public function getRegionRates($shipping_service)
    {
        $rates = Rate::where([
            ['shipping_service_id', $shipping_service->id],
            ['region_id', '!=', null]
        ])->paginate(15);
        
        return $rates;
    }

    public function getPostNLCountryRates($shipping_service)
    {
        $rates = Rate::where([
            ['shipping_service_id', $shipping_service->id],
        ])->paginate(15);
        
        return $rates;
    }

}