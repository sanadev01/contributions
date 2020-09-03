<?php

namespace App\Http\Livewire\User\Profit;

use App\Models\ProfitPackage;
use App\Models\User;
use Livewire\Component;

class Slabs extends Component
{
    public $profitId;
    public $slabs;

    public function mount($profitId = null)
    {
        $this->slabs = old('slab');

        if ( empty($this->slabs) ){
            $profitPackage = ProfitPackage::find($profitId) ?? new ProfitPackage;
            $this->slabs = json_decode($profitPackage->data, true);
        }
    }

    public function render()
    {
        return view('livewire.user.profit.slabs');
    }

    public function addSlab()
    {
        array_push($this->slabs,[
            'min_weight' => '',
            'max_weight' => '',
            'value' => ''
        ]);
    }

    public function removeSlab($index)
    {
        unset($this->slabs[$index]);
    }
}
