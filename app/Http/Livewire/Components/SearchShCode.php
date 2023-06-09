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
        $lang = app()->getLocale();
        $codes = ShCode::get(['id', 'code', 'description'])
        ->map(function ($shCode) use ($lang) {
            $descriptions = explode('-------', $shCode->description);
            $description = '';
            switch ($lang) {
                case 'en':
                    $description = $descriptions[0];
                    break;
                case 'pt':
                    $description = $descriptions[1];
                    break;
                case 'es':
                    $description = $descriptions[2];
                    break;
                default:
                    $description = $descriptions[0];
            }
            $shCode->description = $description;

            return $shCode;
        })->sortBy('description')->values();
        return view('livewire.components.search-sh-code',[
            'codes' => $codes
        ]);
    }

    public function updatedsearch()
    {
        $this->dispatchBrowserEvent('checkShCode', ['sh_code' => $this->search]);
    }
}
