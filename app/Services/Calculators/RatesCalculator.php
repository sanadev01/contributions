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

    protected static $errors;

    public function __construct(Order $order,ShippingService $service, $calculateOnVolumeMetricWeight = true )
    {
        $this->order = $order;
        $this->shippingService = $service;

        $this->recipient = $order->recipient;

        $this->rates = $service->rates()->byCountry($this->recipient->country_id)->first();

        $this->initializeDims();

        $this->weight = $calculateOnVolumeMetricWeight ? $this->calculateWeight(): $this->originalWeight;
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
    private function calculateWeight()
    {
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
        $profitPackage = $user->profitPackage;

        if ( !$profitPackage ){
            return ProfitPackage::where('type',ProfitPackage::TYPE_DEFAULT)->first();
        }

        return $profitPackage;
    }

    public function getProfitSlabValue($profitPackage)
    {
        $weight = ceil(WeightCalculator::kgToGrams($this->weight));
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
                self::$errors .= "Service not available for this Country <br>";
                return false;
            }

            if ( $this->shippingService->max_weight_allowed < $this->weight ){
                self::$errors .= "service is not available for more then {$this->shippingService->max_weight_allowed}KG  weight";
                return false;
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
