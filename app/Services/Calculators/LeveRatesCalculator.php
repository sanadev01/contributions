<?php

namespace App\Services\Calculators;

use Exception;
use function ceil;
use App\Models\Order;
use App\Models\BpsRate;
use App\Models\Profit;
use App\Models\Setting;

class LeveRatesCalculator extends AbstractRateCalculator
{
    private $BpsRates;

    public function __construct(Order $order)
    {
        $this->BpsRates = BpsRate::first();
        $this->setName('Light Service');
        $this->setServiceId(self::SERVICE_BPS);

        parent::__construct($order);
    }

    public function getRate($addProfit = true)
    {
        $rate = 0;
        $weight = ceil(WeightCalculator::kgToGrams($this->weight));

        $rate = collect($this->BpsRates->rates)->where('weight','>=',$weight)->take(1)->first();

        $rate = $rate['bps'] ? $rate['bps'] : 0;
        
        if (! $addProfit) {
            return $rate;
        }
        
        return $rate + $this->getProfitOn($rate);
    }

    public function getProfitOn($cost)
    {
        $weight = ceil(WeightCalculator::kgToGrams($this->weight));
        $user = $this->order->user;

        $profitPackage = $user->profitPackage ? $user->profitPackage : Profit::where('type',Profit::TYPE_DEFAULT)->first();
        $profitSlab = $profitPackage->slabs()->where('min_weight','<=',$weight)->where('max_weight','>=',$weight)->first();

        $profitPercentage =  $profitSlab ? $profitSlab->value : ( $profitPackage ? $profitPackage->slabs()->where('max_weight','>=',29999)->first()->value: 0 );

        $profitAmount =  ($profitPercentage/100) * $cost ;

        return $profitAmount;
    }

    private function weightBetween($weight, $min, $max)
    {
        if ($weight >= $min && $weight <= $max) {
            return true;
        }

        return false;
    }

    public function isAvailable()
    {
        try {
            if (! setting(Setting::SHIPPING_SERVICE_PREFIX.$this->getServiceId(), null, null, true)) {
                return false;
            }

            if (! $this->checkCountry()) {
                \Log::info('fail country');
                return false;
            }

            // if (! $this->checkMinSize()) {
            //     \Log::info('fail size');
            //     return false;
            // }

            if (! $this->combinedDimSize()) {
                \Log::info('fail combined dim size');
                return false;
            }

            if (! $this->maxAllowedSizePerDim()) {
                \Log::info('fail maximum one side dim');
                return false;
            }

            if (! $this->maxWeight()) {
                \Log::info('fail max weight');
                return false;
            }

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function checkCountry()
    {
        return $this->order->address->country->code == 'BR';
    }

    private function checkMinSize()
    {
        $minimumSize = $this->BpsRates->minimum_size;

        $sizeParts = explode('x', $minimumSize);
        $minLength = isset($sizeParts[0]) ? $sizeParts[0] : 0;
        $minWidth = isset($sizeParts[1]) ? $sizeParts[1] : 0;

        $allDims = collect([$this->length, $this->width, $this->height]);

        $matchedCount = 0;
        $allDims = $allDims->reject(function ($item) use ($minLength,&$matchedCount) {
            if ($item >= $minLength) {
                $matchedCount++;
                return true;
            }
        });
        $allDims = $allDims->reject(function ($item) use ($minWidth,&$matchedCount) {
            if ($item >= $minWidth) {
                $matchedCount++;
                return true;
            }
        });

        if ($matchedCount >= 2) {
            return true;
        }

        return false;
    }

    public function combinedDimSize()
    {
        $maxDimSize = $this->BpsRates->max_combine_dim;

        return ($this->length + $this->height + $this->width) < $maxDimSize;
    }

    public function maxAllowedSizePerDim()
    {
        $maxPerSideLimit = $this->BpsRates->max_single_dim;
        return $this->height <= $maxPerSideLimit && $this->width <= $maxPerSideLimit && $this->length <= $maxPerSideLimit;
    }

    public function maxWeight()
    {
        $maxAllowedWeight = WeightCalculator::gramToKgs($this->BpsRates->max_weight);

        return $this->weight <= $maxAllowedWeight;
    }

    public function weightUnit()
    {
        return 'Grams';
    }

    public function weightInDefaultUnit()
    {
        return WeightCalculator::kgToGrams($this->weight);
    }
}
