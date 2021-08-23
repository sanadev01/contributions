<?php

namespace App\Repositories\Reports;

use App\Models\User;
use App\Models\Recipient;
use App\Models\Order;
use App\Models\ProfitPackage;
use App\Models\ShippingService;
use App\Services\Calculators\RatesCalculator;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\CollectsResources;

class RateReportsRepository
{
    protected $error;

    public function getRateReport($packageId)
    {
        $package = ProfitPackage::find($packageId);
        // dd($package->shippingService);
        $recipient = new Recipient();
        $recipient->state_id = 508;//$request->state_id;
        $recipient->country_id = 30;//$request->country_id;
        
        $newUser = Auth::user();
        $newUser->profitPackage = $package;
        $profitPackageSlabRates = collect();

        foreach($package->data as $profitPackageSlab){
            $order = new Order();
            $order->user = $newUser;
            $order->width =  0;
            $order->height = 0;
            $order->length = 0;
            $order->measurement_unit = 'kg/cm';
            $order->recipient = $recipient;
            $originalWeightMax =  $profitPackageSlab['max_weight'];
            $originalWeight =  $profitPackageSlab['min_weight'];
            $profitValue =  $profitPackageSlab['value'];
            if($originalWeight < 100 ){
                $originalWeight = 100;
            }
            $order->weight = UnitsConverter::gramsToKg($originalWeight);
            $shippingRates = collect();
            $shippingValue = collect();
            if($package->shippingService){
                $shippingService = $package->shippingService; 
                $shippingService->cacheCalculator = false;
                if ( $shippingService->isAvailableFor($order) ){
                    $rate = $shippingService->getRateFor($order,true,false);
                    $value = $shippingService->getRateFor($order,false,false);
                    $shippingRates->push($rate);
                    $shippingValue->push($value);
                }
            }else{
                foreach (ShippingService::query()->active()->get() as $shippingService) {
                    $shippingService->cacheCalculator = false;
                    if ( $shippingService->isAvailableFor($order) ){
                        $rate = $shippingService->getRateFor($order,true,false);
                        $value = $shippingService->getRateFor($order,false,false);
                        $shippingRates->push($rate);
                        $shippingValue->push($value);
                    }
                }
            }

            $profitPackageSlabRates->push([
                'weight' => $originalWeightMax,
                'profit'  => $profitValue,
                'shipping'  => $shippingValue,
                'rates'  => $shippingRates,
            ]);
        }

        return $profitPackageSlabRates;
    }
    
    public function getRateSample($serviceId)
    {
        $shippingService = ShippingService::find($serviceId);
        // dd($shippingService->rates[0]->country_id);
        $package = ProfitPackage::find(2);
        
        $recipient = new Recipient();
        $recipient->state_id = 508;//$request->state_id;
        $recipient->country_id = 30;//$request->country_id;
        
        $newUser = Auth::user();
        $newUser->profitPackage = $package;
        $profitPackageSlabRates = collect();

        foreach($package->data as $profitPackageSlab){
            $order = new Order();
            $order->user = $newUser;
            $order->width =  0;
            $order->height = 0;
            $order->length = 0;
            $order->measurement_unit = 'kg/cm';
            $order->recipient = $recipient;
            $originalWeightMax =  $profitPackageSlab['max_weight'];
            $originalWeight =  $profitPackageSlab['min_weight'];
            $profitValue =  $profitPackageSlab['value'];
            if($originalWeight < 100 ){
                $originalWeight = 100;
            }
            $order->weight = UnitsConverter::gramsToKg($originalWeight);
            $shippingRates = collect();
            $shippingValue = collect();
            
            if($shippingService){
                $shippingService->cacheCalculator = false;
                if ( $shippingService->isAvailableFor($order) ){
                    $rate = $shippingService->getRateFor($order,true,false);
                    $value = $shippingService->getRateFor($order,false,false);
                    $shippingRates->push($rate);
                    $shippingValue->push($value);
                }
            }

            $profitPackageSlabRates->push([
                'weight' => $originalWeightMax,
                'profit'  => $profitValue,
                'shipping'  => $shippingValue,
                'rates'  => $shippingRates,
            ]);
        }

        return $profitPackageSlabRates;
    }
}
