<?php

namespace App\Http\Livewire\Tax;

use Livewire\Component;

class Tax extends Component
{
    public $user_id;
    public $trackings;

    public function render()
    {
        return view('livewire.tax.tax');
    }
    public function sreach()
    {
        dd($this);
    }
}
