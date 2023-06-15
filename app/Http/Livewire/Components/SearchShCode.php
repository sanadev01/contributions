<?php

namespace App\Http\Livewire\Components;

use App\Models\ShCode;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

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
        switch ($lang) {
            case 'en':
                $langIndex = 0;
                break;
            case 'pt':
                $langIndex = 1;
                break;
            case 'es':
                $langIndex = 2;
                break;
            default:
                $langIndex = 0;
        }

        $codes = ShCode::select('id', 'code', DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(description, '-------', $langIndex+1), '-------', -1) as description"))
            ->orderBy('description')
            ->get();
            
        return view('livewire.components.search-sh-code',[
            'codes' => $codes
        ]);
    }

    public function updatedsearch()
    {
        $this->dispatchBrowserEvent('checkShCode', ['sh_code' => $this->search]);
    }
}
