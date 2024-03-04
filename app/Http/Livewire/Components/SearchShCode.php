<?php

namespace App\Http\Livewire\Components;

use App\Models\ShCode;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class SearchShCode extends Component
{
    public $search;
    public $name;
    public $type='Postal (Correios)';
    public $orderInventory = false; 
    protected $listeners = ['reloadSHCodes' => 'reloadSHCodes'];

    public function reloadSHCodes($data)
    { 
        $service = optional($data)['service'];
        $shippingService = ShippingService::where('service_sub_class',$service)->first();
        if(optional($shippingService)->is_total_express){
            $this->type = 'Courier';
        }else{
            $this->type= 'Postal (Correios)';
        }
  
        $this->render();
        $this->dispatchBrowserEvent('shCodeReloaded');
    }
    public function mount($code= null,$name=null, $order = null)
    {
        $shCode = null;
        $this->valid = false;

        $this->name = $name;
        
        if ( $code ){
            $shCode = ShCode::where('code',$code)->first();
        }
        if ($order && $order->products->isNotEmpty()) {
            $this->orderInventory = true;
        }

        $this->search = old('sh_code', optional($shCode)->code );
    }

    public function render()
    {
        return view('livewire.components.search-sh-code',[
             'codes' => Cache::remember($this->type,120,function(){ 
                return ShCode::where('type',$this->type)->orderBy('description','ASC')->get();
            })

        ]);
    }

    public function updatedsearch()
    {
        $this->dispatchBrowserEvent('checkShCode', ['sh_code' => $this->search]);
    }
}
