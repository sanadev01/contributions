<?php

namespace App\Http\Livewire\User\Profit;

use Livewire\Component;

class SlabField extends Component
{

    public $slab;
    public $key;
    public $profit;

    public function mount($slab)
    {
        $this->slab = $slab;
        // dd($this->profit);
    }
    
    public function render()
    {
        // dd($this->profit);
        return view('livewire.user.profit.slab-field');
    }

}
