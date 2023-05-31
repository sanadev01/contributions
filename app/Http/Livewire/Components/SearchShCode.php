<?php

namespace App\Http\Livewire\Components;

use App\Models\ShCode;
use Livewire\Component;
use App\Models\ShippingService;

class SearchShCode extends Component
{
    public $search;
    public $name;
    public $orderInventory = false;
    public $codes;

    protected $listeners = ['getShCodes'];

    public function mount($code= null,$name=null, $order = null, $service = null)
    {
        $shCode = null;
        $this->valid = false;

        $this->name = $name;
        $this->service = $service;

        
        if ( $code ){
            $shCode = ShCode::where('code',$code)->first();
        }
        if ($order && $order->products->isNotEmpty()) {
            $this->orderInventory = true;
        }

        $this->search = old('sh_code', optional($shCode)->code );
        
        $this->codes = ShCode::whereRaw('CHAR_LENGTH(code) = ?', [6])->orderBy('description','ASC')->get();
    }

    public function getShCodes($service)
    {
        if($service == ShippingService::Post_Plus_Prime) {
            $this->codes = ShCode::whereRaw('CHAR_LENGTH(code) = ?', [10])->orderBy('description','ASC')->get();
        }else {
            $this->codes = ShCode::whereRaw('CHAR_LENGTH(code) = ?', [6])->orderBy('description','ASC')->get();
        }
        return $this->render();
    }

    public function render()
    {
        $codes = $this->codes;
        return view('livewire.components.search-sh-code',[
            'codes' => $this->codes
        ]);
    }

    public function updatedsearch()
    {
        $this->dispatchBrowserEvent('checkShCode', ['sh_code' => $this->search]);
    }
}
