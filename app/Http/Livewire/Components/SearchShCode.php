<?php

namespace App\Http\Livewire\Components;

use App\Models\ShCode;
use Livewire\Component;

class SearchShCode extends Component
{
    public $search;
    public $name;
    public $orderInventory = false;

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
            'codes' => ShCode::orderBy('description','ASC')->get()
        ]);
    }

    public function updatedsearch()
    {
        $this->dispatchBrowserEvent('checkShCode', ['sh_code' => $this->search]);
    }
}
