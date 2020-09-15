<?php

namespace App\Http\Livewire\Components;

use App\Models\ShCode;
use Livewire\Component;

class SearchShCode extends Component
{
    public $search;
    public $codesList;
    public $name;

    public function mount($code= null,$name=null)
    {
        $shCode = null;
        $this->name = $name;
        
        if ( $code ){
            $shCode = ShCode::where('code',$code)->first();
        }

        $this->search = old('sh_code', optional($shCode)->code );
    }

    public function render()
    {
        return view('livewire.components.search-sh-code');
    }

    public function updatedSearch()
    {
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
    }
}
