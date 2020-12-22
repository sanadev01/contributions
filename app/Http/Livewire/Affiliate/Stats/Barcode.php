<?php

namespace App\Http\Livewire\Affiliate\Stats;

use App\Models\User;
use Livewire\Component;

class Barcode extends Component
{
    public $refferCode;

    public function mount($refferCode)
    {
        $this->refferCode = $refferCode;
    }
    public function render()
    {
        return view('livewire.affiliate.stats.barcode',[
            'barcode' => User::getBarcode($this->refferCode)
        ]);
    }
}
