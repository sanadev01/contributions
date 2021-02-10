<?php

namespace App\Http\Livewire\Affiliate\Stats;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Barcode extends Component
{
    public $refferCode;

    public function mount()
    {
        $this->refferCode = Auth::user()->getRefferCode();
    }
    public function render()
    {
        return view('livewire.affiliate.stats.barcode',[
            'barcode' => User::getBarcode($this->refferCode)
        ]);
    }
}
