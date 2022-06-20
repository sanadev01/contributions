<?php

namespace App\Http\Livewire\Affiliate\Stats;

use Illuminate\Support\Facades\Auth;
use App\Models\CommissionSetting;
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

            $query = CommissionSetting::query();
            $query->where('user_id', Auth::id());
            $commission = $query->first();
            
            return $commission ? $commission->commission_balance : 0;
            
        }
        
    }
}
