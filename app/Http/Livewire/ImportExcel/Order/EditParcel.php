<?php

namespace App\Http\Livewire\ImportExcel\Order;

use App\Models\ImportedOrder;
use App\Services\Calculators\WeightCalculator;
use App\Services\Converters\UnitsConverter;
use Livewire\Component;

class EditParcel extends Component
{
    public $orderId; 
    public $order;

    public $merchant;
    public $carrier;
    public $tracking_id;
    public $customer_reference;
    public $order_date;
    public $whr_number;
    public $weight;
    public $unit;
    public $length;
    public $width;
    public $height;

    public $weightOther;
    public $lengthOther;
    public $widthOther;
    public $heightOther;
    public $volumeWeight;
    public $currentWeightUnit;

    public function mount()
    {
        $order = ImportedOrder::find(8);
        $this->order = $order;
        $this->fillData();
    }

    public function render()
    {
        return view('livewire.import-excel.order.edit-parcel');
    }

    public function parcel()
    {
        $data = $this->validate([
            'merchant' => 'required',
            'carrier' => 'required',
            'tracking_id' => 'required',
            'customer_reference' => 'required',
            'order_date' => 'required',
            'weight' => 'required',
            'unit' => 'required',
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
        ]);
        
        $this->order->update($data);
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
        $this->merchant = old('merchant' );
        $this->carrier = old('carrier');
        $this->tracking_id = old('tracking_id');
        $this->customer_reference = old('customer_reference');
        $this->order_date = old('order_date');
        $this->whr_number = old('whr_number');
        $this->weight = old('weight', isset($this->order->weight) ? $this->order->weight : 0 );
        $this->length = old('length',isset($this->order->length) ? $this->order->length : 0);
        $this->width = old('width',isset($this->order->width) ? $this->order->width : 0);
        $this->height = old('height',isset($this->order->height) ? $this->order->height : 0);
        $this->unit = old('unit',isset($this->order->measurement_unit) ? $this->order->measurement_unit : 'lbs/in');
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
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->length,$this->width,$this->height,'in');
            $this->volumeWeight = round($volumetricWeight > $this->weight ? $volumetricWeight : $this->weight,2);
        }
    }
}
