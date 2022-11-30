<?php

namespace App\Services\Calculators;

use App\Models\Order;
use App\Models\Profit;
use App\Models\ProfitPackage;
use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
use Exception;
use function ceil;

class RatesCalculator
{
    protected $order;
    protected $shippingService;

    protected $length;
    protected $width;
    protected $height;

    protected $originalWeight;

    protected $weight;

    protected $shipment;

    protected $recipient;

    protected $rates;

    protected $calculateOnVolumeMetricWeight;

    protected $discountPercentage;

    protected static $errors;

    public function __construct(Order $order,ShippingService $service, $calculateOnVolumeMetricWeight = true, $originalRate = false )
    {
        $this->order = $order;

        if ($service && $service->service_sub_class == ShippingService::AJ_Packet_Standard) {

            $this->shippingService = ShippingService::where('service_sub_class', ShippingService::Packet_Standard)->first();

        }elseif($service && $service->service_sub_class == ShippingService::AJ_Packet_Express){

            $this->shippingService = ShippingService::where('service_sub_class', ShippingService::Packet_Express)->first();

        }else {

            $this->shippingService = $service;
        }

        $this->recipient = $order->recipient;

        if($this->recipient->commune_id != null)
        {
            $this->rates = $service->rates()->byRegion($this->recipient->country_id, optional($this->recipient->commune)->region->id)->first();

        }else{
            $this->rates = $this->shippingService->rates()->byCountry($this->recipient->country_id)->first();
        }

        $this->initializeDims();

        $this->weight = $calculateOnVolumeMetricWeight ? $this->calculateWeight($originalRate): $this->originalWeight;
    }

    private function initializeDims()
    {
        if ($this->order->measurement_unit == 'lbs/in') {
            $this->length = UnitsConverter::inToCm($this->order->length);
            $this->width = UnitsConverter::inToCm($this->order->width);
            $this->height = UnitsConverter::inToCm($this->order->height);
            $this->originalWeight = UnitsConverter::poundToKg($this->order->weight);
        } else {
            $this->length = $this->order->length;
            $this->width = $this->order->width;
            $this->height = $this->order->height;
            $this->originalWeight = $this->order->weight;
        }
    }

    /**
     * Calculate Rates. and rate is always in kg
     * because we converted dimensions to cm on above
     */
    private function calculateWeight($originalRate)
    {
        if ($this->order->weight_discount && $originalRate == false)
        {
            $unit = ($this->order->measurement_unit == 'lbs/in') ? 'in' : 'cm';

            $volumnWeight = WeightCalculator::getVolumnWeight($this->order->length, $this->order->width, $this->order->height, $unit);

            $consideredWeight = $volumnWeight - $this->order->getOriginalWeight();

            $volumnWeight = round($consideredWeight - $this->order->weight_discount, 2);

            $volumnWeight = $volumnWeight + $this->order->getOriginalWeight();

            $volumnWeight = ($this->order->measurement_unit == 'lbs/in') ? UnitsConverter::poundToKg($volumnWeight) : $volumnWeight;

            return $volumnWeight;
        }

        $volumnWeight = WeightCalculator::getVolumnWeight($this->length, $this->width, $this->height);

        return $volumnWeight > $this->originalWeight ? $volumnWeight : $this->originalWeight;
    }

    /**
     * @return float|int
     */
    public function getWeight($unit = 'kg')
    {
        if ($unit == 'kg') {
            return $this->weight;
        }

        if ($unit == 'lbs') {
            return UnitsConverter::kgToPound($this->weight);
        }

        return  'Invalid Unit';
    }


    public function getRate($addProfit = true)
    {
        if (!$this->rates) {
            return null;
        }
        $rate = 0;
        $weight = ceil(WeightCalculator::kgToGrams($this->weight));

        if ( $weight<100 ){
            $weight = 100;
        }

        $rates = collect($this->rates->data)->where('weight','<=',$weight)->sortByDesc('weight')->take(2);
        $secRate = [];
        foreach($rates as $rate){
            $secRate[] = $rate;
        }
        if($this->order->id){
            if(optional($secRate)[1]){
                $rate = $secRate[1]['leve'];
            }else{
                $rate = $secRate[0]['leve'];
            }
        }else{
            $rate = collect($this->rates->data)->where('weight','<=',$weight)->sortByDesc('weight')->take(1)->first();
            $rate = $rate['leve'];
        }


        if (! $addProfit) {
            return $rate;
        }
        return $rate + $this->getProfitOn($rate);
    }

    public function getProfitOn($cost)
    {
        $profitPackage = $this->getProfitPackage();

        if ( !$profitPackage ){
            return 0;
        }

        $profitPercentage =  $this->getProfitSlabValue($profitPackage);

        $profitAmount =  ($profitPercentage/100) * $cost ;

        return $profitAmount;
    }

    public function getProfitPackage()
    {
        $user = $this->order->user;

        $shippingServiceId = $this->shippingService->id;

        $profitSetting = $this->order->user->profitSettings()->where('user_id',$user->id)->where('service_id',$shippingServiceId)->first();
        if($profitSetting){
            $profitPackage =$profitSetting->profitPackage;
        }else{
            $profitPackage = $user->profitPackage;
        }

        if ( !$profitPackage ){
            return ProfitPackage::where('type',ProfitPackage::TYPE_DEFAULT)->first();
        }

        return $profitPackage;
    }

    public function getProfitSlabValue($profitPackage)
    {
        $weight = ceil(WeightCalculator::kgToGrams($this->weight));
        if ( $weight<100 ){
            $weight = 100;
        }
        $profitSlab = collect($profitPackage->data)->where('max_weight','<=',$weight)->sortByDesc('min_weight')->first();

        if ( !$profitSlab ){
            $profitSlab = collect($profitPackage->data)->where('max_weight','>=',29999)->first();
        }

        if ( !$profitSlab ){
            return 0;
        }

        return optional($profitSlab)['value'];

    }

    public function isAvailable()
    {
        try {

            if ( !$this->rates ) {
                // self::$errors .= "Service not available for this Country <br>";
                return false;
            }

            $profitSetting = $this->order->user->profitSettings->where('service_id',$this->shippingService->id)->first();
            if(!$profitSetting && !auth()->user()->isAdmin()){
                return false;
            }
            if ( $this->shippingService->max_weight_allowed < $this->weight ){
                self::$errors .= "service is not available for more then {$this->shippingService->max_weight_allowed}KG  weight";
                return false;
            }

            /**
             * Sinerlog modification
             * Validation for sinerlog services
             */
            /**
             * Standard service
             * "weight":["The weight must be between 1 and 30000."],
             * "height":["The height must be between 1 and 105."],
             * "width":["The width must be between 9 and 105."],
             * "length":["The length must be between 14 and 105."]
             */

            /**
             * Express service
             * "weight":["The weight must be between 1 and 30000."]
             * "height":["The height must be between 1 and 105."]
             * "width":["The width must be between 9 and 105."]
             * "length" :["The length must be between 14 and 105."]
             */

            /**
             * Small package
             * "weight":["The weight must be between 1 and 300."]
             * "height":["The height must be between 1 and 4."]
             * "width":["The width must be between 10 and 16."]
             * "length":["The length must be between 15 and 24."]
             * "products.0.value":["The products.0.value must be between 0.01 and 50.00."]
             */
            if ( $this->shippingService->api == 'sinerlog' ) {
                if($this->height < $this->shippingService->min_height_allowed || $this->height > $this->shippingService->max_height_allowed){
                    return false;
                }

                if ($this->width < $this->shippingService->min_width_allowed || $this->width > $this->shippingService->max_width_allowed) {
                    return false;
                }

                if ($this->length < $this->shippingService->min_length_allowed || $this->length > $this->shippingService->max_length_allowed) {
                    return false;
                }

                if (($this->width + $this->height + $this->length) > $this->shippingService->max_sum_of_all_sides) {
                    return false;
                }
            }

            return true;
        } catch (Exception $exception) {
            self::$errors .= "Error: ".$exception->getMessage();
            return false;
        }
    }

    public function weightUnit()
    {
        return 'Grams';
    }

    public function weightInDefaultUnit()
    {
        return WeightCalculator::kgToGrams($this->weight);
    }

    public function getErrors()
    {
        return self::$errors;
    }
}
