<?php

namespace App\Http\Livewire\Calculator;

use App\Models\Order;
use App\Services\Calculators\WeightCalculator;
use App\Services\Converters\UnitsConverter;
use Livewire\Component;

class Calculation extends Component
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
    public $currentWeightUnit;

    public function mount(Order $order = null)
    {
        $this->order = optional($order)->toArray();
        $this->fillData();
    }

    public function render() 
    {
        return view('livewire.calculator.calculation');
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
        $this->weight = old('weight', isset($this->order['weight']) ? $this->order['weight'] : 0 );
        $this->length = old('length',isset($this->order['length']) ? $this->order['length'] : 0);
        $this->width = old('width',isset($this->order['width']) ? $this->order['width'] : 0);
        $this->height = old('height',isset($this->order['height']) ? $this->order['height'] : 0);
        $this->unit = old('unit',isset($this->order['measurement_unit']) ? $this->order['measurement_unit'] : 'kg/cm');
        $this->calculateOtherUnits();
    }


    public function calculateOtherUnits()
    {
        $this->weight = $this->weight ? $this->weight : 0;
        $this->length = $this->length ? $this->length : 0;
        $this->width = $this->width ? $this->width : 0;
        $this->height = $this->height ? $this->height : 0;

        if ( $this->unit == 'kg/cm' ){
            $this->weightOther = UnitsConverter::kgToPound($this->weight);
            $this->lengthOther = UnitsConverter::cmToIn($this->length);
            $this->widthOther = UnitsConverter::cmToIn($this->width);
            $this->heightOther = UnitsConverter::cmToIn($this->height);
            $this->currentWeightUnit = 'kg';
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->length,$this->width,$this->height,'cm');
            $this->volumeWeight = round($volumetricWeight > $this->weight ? $volumetricWeight : $this->weight,2);
        }else{
            $this->weightOther = UnitsConverter::poundToKg($this->weight);
            $this->lengthOther = UnitsConverter::inToCm($this->length);
            $this->widthOther = UnitsConverter::inToCm($this->width);
            $this->heightOther = UnitsConverter::inToCm($this->height);
            $this->currentWeightUnit = 'lbs';
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->length,$this->width,$this->height,'in');;
            $this->volumeWeight = round($volumetricWeight > $this->weight ? $volumetricWeight : $this->weight,2);
        }
    }
}
