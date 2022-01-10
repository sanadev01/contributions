<?php

namespace App\Http\Livewire\Components;

use App\Models\ShCode;
use Livewire\Component;

class SearchShCode extends Component
{
    public $search;
    public $name;

    public function mount($code= null,$name=null)
    {
        $shCode = null;
        $this->valid = false;

        $this->name = $name;
        
        if ( $code ){
            $shCode = ShCode::where('code',$code)->first();
        }

        $this->search = old('sh_code', optional($shCode)->code );
    }

    public function render()
    {
        return view('livewire.components.search-sh-code',[
            'codes' => ShCode::all()
        ]);
    }

    public function updatedsearch()
    {
        $this->dispatchBrowserEvent('checkShCode', ['sh_code' => $this->search]);
    }
}
