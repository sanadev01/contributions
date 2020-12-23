<?php

namespace App\Http\Livewire\Affiliate\Stats;

use Illuminate\Support\Facades\Auth;
use App\Models\AffiliateSale;
use Livewire\Component;

class AdminSale extends Component
{
    public function render()
    {
        return view('livewire.affiliate.stats.admin-sale',[
            'adminCommission' => $this->getSumCommission()
        ]);
        
    }
    public function getSumCommission()
    {
        
        if (Auth::user()->isAdmin()) {

            $query = AffiliateSale::query();
            $query->where('user_id', Auth::id());
            return $query->get()->sum('commission');
            
        }
        
    }
}
