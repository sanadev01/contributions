<?php

namespace App\Http\Livewire\User\Profit;

use Livewire\Component;
use App\Models\Recipient;
use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;
use App\Services\Converters\UnitsConverter;

class SlabField extends Component
{

    public $slab = [];
    public $key;
    public $profit;
    public $shipping;
    public $package;
    public $sale;
    public $rate;
    public $weight;

    public function mount($slab,$index, $package)
    {
        $this->slab = $slab;
        $this->key = $index;
        $this->profit = $slab['value'];
        $this->weight = $slab['max_weight'];
        $this->package = $package;
         $this->sale = $this->getSaleRate($this->package, $this->weight, true);
         $this->shipping = $this->getSaleRate($this->package, $this->weight, false);
        
    }

    public function render()
    {
        return view('livewire.user.profit.slab-field');
    }

    public function calculateRate()
    {
        $rate = $this->profit*($this->shipping/100)+$this->profit;
        return $rate;
    }

    public function getSaleRate($package, $weight, $isRate)
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
                $rate = $shippingService->getRateFor($order,$isRate,false);
                return $rate;
            }
        }

    }

}
