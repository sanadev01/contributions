<?php

namespace App\Http\Livewire\Components;

use App\Models\ShCode;
use Livewire\Component;

class SearchShCode extends Component
{
    public $search;
    public $codesList;
    public $name;
    public $valid;

    public function mount($code= null,$name=null)
    {
        $shCode = null;
        $this->valid = false;

        $this->name = $name;
        
        if ( $code ){
            $shCode = ShCode::where('code',$code)->first();
        }

        if ( $shCode ){
            $this->valid = true;
        }

        $this->search = old('sh_code', optional($shCode)->code );
    }

    public function render()
    {
        return view('livewire.components.search-sh-code');
    }

    public function updatedSearch()
    {
        $this->valid = false;

        if ( !$this->search ){
            $this->codesList = [];
            return;
        }

        $this->codesList = ShCode::query()
            ->where(function($query){
                $query->where('code','LIKE',"%{$this->search}%")
                    ->orWhere('description','LIKE',"%{$this->search}%");
            })
            ->get()
            ->toArray();
    }

    public function selectCode($code)
    {
        $this->search = $code;
        $this->codesList = [];
        $this->valid = true;
    }
}
