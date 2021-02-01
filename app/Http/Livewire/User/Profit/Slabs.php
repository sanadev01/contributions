<?php

namespace App\Http\Livewire\User\Profit;

use App\Models\Order;
use Livewire\Component;
use App\Models\Recipient;
use App\Models\ProfitPackage;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use App\Services\Converters\UnitsConverter;

class Slabs extends Component
{
    public $profitId;
    public $slabs;
    public $profitPackage;
    public $profit;

    public function mount($profitId = null)
    {
        $this->slabs = old('slab',[]);
        if ( $profitId ){
            $profitPackage = ProfitPackage::find($profitId) ?? new ProfitPackage;
            $this->slabs = array_unique(array_merge($profitPackage->data,$this->slabs),SORT_REGULAR);
            $this->profitPackage = $profitPackage;
        }

    }

    public function render()
    {
        // dd($this->profit);
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
