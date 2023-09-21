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
    public $edit;

    public $merchant;
    public $carrier;
    public $tracking_id;
    public $customer_reference;
    public $correios_tracking_code;
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

    public function mount($order, $edit= '')
    {
        $this->edit = $edit;
        $this->order = $order;
        $this->fillData();
    }

    public function render()
    {
        return view('livewire.import-excel.order.edit-parcel');
    }

    public function save()
    {
        $rules = [
            'merchant' => 'required',
            'carrier' => 'required',
            'tracking_id' => 'required',
            'customer_reference' => 'nullable',
            'correios_tracking_code' => 'nullable',
            'order_date' => 'required',
            'weight' => 'required',
            'unit' => 'required',
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
        ];
        $customMessages = [
            'merchant.required' => 'merchant is required',
            'carrier.required' => 'carrier is required',
            'tracking_id.required' => 'tracking id is required',
            'customer_reference.nullable' => 'customer reference is required',
            'correios_tracking_code.nullable' => 'correios tracking code required',
            'measurement_unit.required' => 'measurement unit is required',
            'weight.required' => 'weight is required',
            'length.required' => 'length is required',
            'width.required' => 'width is required',
            'height.required' => 'height is required',
        ];
    
        $data = $this->validate($rules, $customMessages);

        $error = $this->order->error;
        if($error){
            $remainError = array_diff($error, $customMessages);
            $error = $remainError ? $remainError : null;
        }

        $this->order->update([
            'merchant' => $data['merchant'],
            'carrier' => $data['carrier'],
            'tracking_id' => $data['tracking_id'],
            'customer_reference' => $data['customer_reference'],
            'order_date' => $data['order_date'],
            'weight' => $data['weight'],
            'measurement_unit' => $data['unit'],
            'length' => $data['length'],
            'width' => $data['width'],
            'height' => $data['height'],
            'correios_tracking_code' => optional($data)['correios_tracking_code'],
            'error' => $error,
        ]);
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
        $this->merchant = optional($this->order)->merchant;
        $this->carrier = optional($this->order)->carrier;
        $this->tracking_id = optional($this->order)->tracking_id;
        $this->customer_reference = optional($this->order)->customer_reference;
        $this->correios_tracking_code = optional($this->order)->correios_tracking_code;
        $this->order_date = optional($this->order)->order_date;
        $this->whr_number = optional($this->order)->whr_number;
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
