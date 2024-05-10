<?php

namespace App\Http\Livewire\Calculator;

use App\Models\Order;
use App\Models\Country;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;
use Illuminate\Support\Facades\Route;

class UsCalculation extends Component
{
    protected $orderId;
    public $weight;
    public $weightOther;
    public $length;
    public $lengthOther;
    public $width;
    public $widthOther;
    public $height;
    public $order_value;
    public $heightOther;
    public $unit;
    public $cc;
    public $unitOther;
    public $volumeWeight;
    public $volumeWeightOther;
    public $currentWeightUnit;

    private $userId;
    public $discountPercentage;
    public $totalDiscountedWeight;

    public function mount($cc = null, Order $order = null)
    {
        $this->cc = $cc;
        $this->order = optional($order)->toArray();
        $this->fillData();
        $this->checkUser();
        if ($this->userId && $this->cc != 'US') {
            $this->setVolumetricDiscount();
        }
    }

    public function render()
    {
       return view('livewire.calculator.us-calculation'); 
    }

    public function updatedUnit()
    {
        $this->calculateOtherUnits();
    }

    public function updatedWeight()
    {
        $this->calculateOtherUnits();
    }

    public function updatedLength()
    {
        $this->calculateOtherUnits();
    }

    public function updatedWidth()
    {
        $this->calculateOtherUnits();
    }

    public function updatedHeight()
    {
        $this->calculateOtherUnits();
    }

    private function fillData()
    {
        $this->weight = old('weight', isset($this->order['weight']) ? $this->order['weight'] : 0);
        $this->length = old('length', isset($this->order['length']) ? $this->order['length'] : 0);
        $this->width = old('width', isset($this->order['width']) ? $this->order['width'] : 0);
        $this->height = old('height', isset($this->order['height']) ? $this->order['height'] : 0);
        $this->unit = old('unit', isset($this->order['measurement_unit']) ? $this->order['measurement_unit'] : 'kg/cm');
        $this->calculateOtherUnits();
    }


    public function calculateOtherUnits()
    {
        $this->weight = $this->weight ? $this->weight : 0;
        $this->length = $this->length ? $this->length : 0;
        $this->width = $this->width ? $this->width : 0;
        $this->height = $this->height ? $this->height : 0;

        if ($this->unit == 'kg/cm') {
            $this->weightOther = UnitsConverter::kgToPound($this->weight);
            $this->lengthOther = UnitsConverter::cmToIn($this->length);
            $this->widthOther = UnitsConverter::cmToIn($this->width);
            $this->heightOther = UnitsConverter::cmToIn($this->height);
            $this->currentWeightUnit = 'kg';
            $this->unitOther = 'lbs';
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->length, $this->width, $this->height, 'cm');
            $this->volumeWeight = round($volumetricWeight > $this->weight ? $volumetricWeight : $this->weight, 2);
            $this->volumeWeightOther = UnitsConverter::kgToPound($this->volumeWeight);

            if ($this->discountPercentage && $this->discountPercentage > 0) {

                if ($this->discountPercentage == 1) {
                    return $this->volumeWeight = $this->weight;
                }

                if ($this->volumeWeight > $this->weight) {
                    $this->calculateDiscountedWeight();
                }
            }
        } else {
            $this->weightOther = UnitsConverter::poundToKg($this->weight);
            $this->lengthOther = UnitsConverter::inToCm($this->length);
            $this->widthOther = UnitsConverter::inToCm($this->width);
            $this->heightOther = UnitsConverter::inToCm($this->height);
            $this->currentWeightUnit = 'lbs';
            $this->unitOther = 'kg';
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->length, $this->width, $this->height, 'in');;
            $this->volumeWeight = round($volumetricWeight > $this->weight ? $volumetricWeight : $this->weight, 2);
            $this->volumeWeightOther = UnitsConverter::poundToKg($this->volumeWeight);

            if ($this->discountPercentage && $this->discountPercentage > 0) {

                if ($this->discountPercentage == 1) {
                    return $this->volumeWeight = $this->weight;
                }

                if ($this->volumeWeight > $this->weight) {
                    $this->calculateDiscountedWeight();
                }
            }
        }
    }

    public function checkUser()
    {
        if (Auth::check()) {

            $this->userId = Auth::user()->id;
            $country_id = Auth::user()->country_id;
            if ($country_id) {
                $country = Country::find($country_id);
                if ($country->id == Country::US) {
                    return $this->unit = 'lbs/in';
                }
            }
        }

        return;
    }

    private function setVolumetricDiscount()
    {
        $volumetricDiscount = setting('volumetric_discount', null, $this->userId);
        $discountPercentage = setting('discount_percentage', null, $this->userId);

        if ($volumetricDiscount && $discountPercentage) {
            $this->discountPercentage = ($discountPercentage) ? $discountPercentage / 100 : 0;
        }

        return true;
    }

    private function calculateDiscountedWeight()
    {
        $consideredWeight = $this->volumeWeight - $this->weight;

        $this->volumeWeight = round($consideredWeight - ($consideredWeight * $this->discountPercentage), 2);
        $this->totalDiscountedWeight = $consideredWeight - $this->volumeWeight;
        $this->volumeWeight = round($this->volumeWeight + $this->weight, 2);
        $this->volumeWeightOther = ($this->unit == 'kg/cm') ? UnitsConverter::kgToPound($this->volumeWeight) : UnitsConverter::poundToKg($this->volumeWeight);
    }
}
