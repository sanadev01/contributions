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

    public function getSaleRate($package, $weight)
    {
        $recipient = new Recipient();
        $recipient->state_id = 508;//$request->state_id;
        $recipient->country_id = 30;//$request->country_id;
        
        $newUser = Auth::user();
        $newUser->profitPackage = $package;

        $order = new Order();
        $order->user = $newUser;
        $order->width =  0;
        $order->height = 0;
        $order->length = 0;
        $order->measurement_unit = 'kg/cm';
        $order->recipient = $recipient;
        $order->weight = UnitsConverter::gramsToKg($weight);

        foreach (ShippingService::query()->active()->get() as $shippingService) {
            $shippingService->cacheCalculator = false;
            if ( $shippingService->isAvailableFor($order) ){
                $rate = $shippingService->getRateFor($order,true,false);
                return $rate;
            }
        }

    }
}
