<?php

namespace App\Http\Livewire\Affiliate\Stats;

use Illuminate\Support\Facades\Auth;
use App\Models\AffiliateSale;
use Livewire\Component;

class Commission extends Component
{
    public function render()
    {

        return view('livewire.affiliate.stats.commission',[
            'commission' => $this->getSumCommission()
        ]);
        
    }
    public function getSumCommission()
    {
        $query = AffiliateSale::query();

        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }
        
        return $query->get()->sum('commission');
    }
}
