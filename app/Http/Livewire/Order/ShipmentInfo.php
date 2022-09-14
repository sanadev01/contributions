<?php

namespace App\Http\Livewire\Order;

use Livewire\Component;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;

class ShipmentInfo extends Component
{

    protected $orderId;

    public $weight;
    public $weightOther;
    public $length;
    public $lengthOther;
    public $width;
    public $widthOther;
    public $height;
    public $heightOther;
    public $unit;
    public $volumeWeight;
    public $actualVolumeWeight;
    public $currentWeightUnit;

    public $discountPercentage;
    public $totalDiscountedWeight;

    public function mount($order = null)
    {
        $this->order = optional($order)->toArray();
        $this->setVolumetricDiscount();
        $this->fillData();
    }

    public function render()
    {
        return view('livewire.order.shipment-info');
    }

    public function updatedUnit()
    {
        $this->calculateOtherUnits();
        $this->emit('updatedUnit', $this->unit);
    }

    public function updatedWeight()
    {
        $this->calculateOtherUnits();
        $this->emit('updatedWeight', $this->weight);
    }

    public function updatedLength()
    {
        $this->calculateOtherUnits();
        $this->emit('updatedLength', $this->length);
    }

    public function updatedWidth()
    {
        $this->calculateOtherUnits();
        $this->emit('updatedWidth', $this->width);
    }

    public function updatedHeight()
    {
        $this->calculateOtherUnits();
        $this->emit('updatedHeight', $this->height);
    }

    private function fillData()
    {
        $this->weight = old('weight', isset($this->order['weight']) ? $this->order['weight'] : 0);
        $this->length = old('length', isset($this->order['length']) ? $this->order['length'] : 0);
        $this->width = old('width', isset($this->order['width']) ? $this->order['width'] : 0);
        $this->height = old('height', isset($this->order['height']) ? $this->order['height'] : 0);
        $this->unit = old('unit', isset($this->order['measurement_unit']) ? $this->order['measurement_unit'] : 'lbs/in');
        $this->calculateOtherUnits();
    }


    public function calculateOtherUnits()
    {
        $this->weight = $this->weight ? $this->weight : 0;
        $this->length = $this->length ? $this->length : 0;
        $this->width = $this->width ? $this->width : 0;
        $this->height = $this->height ? $this->height : 0;
        $this->actualVolumeWeight = null;

        if ($this->unit == 'kg/cm') {
            $this->weightOther = UnitsConverter::kgToPound($this->weight);
            $this->lengthOther = UnitsConverter::cmToIn($this->length);
            $this->widthOther = UnitsConverter::cmToIn($this->width);
            $this->heightOther = UnitsConverter::cmToIn($this->height);
            $this->currentWeightUnit = 'kg';
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->length, $this->width, $this->height, 'cm');
            $this->volumeWeight = round($volumetricWeight > $this->weight ? $volumetricWeight : $this->weight, 2);

            if ($this->discountPercentage && $this->discountPercentage > 0) {

                if ($this->volumeWeight > $this->weight) {

                    if ($this->discountPercentage == 1) {
                        $this->totalDiscountedWeight = $this->volumeWeight - $this->weight;
                        $this->actualVolumeWeight = $this->volumeWeight;
                        return $this->volumeWeight = $this->weight;
                    }

                    $this->calculateDiscountedWeight();
                }
            }
        } else {
            $this->weightOther = UnitsConverter::poundToKg($this->weight);
            $this->lengthOther = UnitsConverter::inToCm($this->length);
            $this->widthOther = UnitsConverter::inToCm($this->width);
            $this->heightOther = UnitsConverter::inToCm($this->height);
            $this->currentWeightUnit = 'lbs';
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->length, $this->width, $this->height, 'in');
            $this->volumeWeight = round($volumetricWeight > $this->weight ? $volumetricWeight : $this->weight, 2);

            if ($this->discountPercentage && $this->discountPercentage > 0) {

                if ($this->volumeWeight > $this->weight) {

                    if ($this->discountPercentage == 1) {
                        $this->totalDiscountedWeight = $this->volumeWeight - $this->weight;
                        $this->actualVolumeWeight = $this->volumeWeight;
                        return $this->volumeWeight = $this->weight;
                    }

                    $this->calculateDiscountedWeight();
                }
            }
        }

        $this->emit('volumeWeight', $this->volumeWeight);
    }

    private function setVolumetricDiscount()
    {
        $userId = ($this->order) ? optional($this->order)['user_id'] : auth()->user()->id;
        $volumetricDiscount = setting('volumetric_discount', null, $userId);
        $discountPercentage = setting('discount_percentage', null, $userId);

        if ($volumetricDiscount && $discountPercentage) {
            $this->discountPercentage = ($discountPercentage) ? $discountPercentage / 100 : 0;
        }

        return true;
    }

    private function calculateDiscountedWeight()
    {
        $this->actualVolumeWeight = $this->volumeWeight;
        $consideredWeight = $this->volumeWeight - $this->weight;

        $this->volumeWeight = round($consideredWeight - ($consideredWeight * $this->discountPercentage), 2);
        $this->totalDiscountedWeight = $consideredWeight - $this->volumeWeight;
        $this->volumeWeight = round($this->volumeWeight + $this->weight, 2);
    }
}
