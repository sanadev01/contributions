<?php

namespace App\Http\Livewire\Affiliate\Stats;


use Illuminate\Support\Facades\Auth;
use App\Models\AffiliateSale;
use Livewire\Component;

class Sale extends Component
{
    public function render()
    {
        return view('livewire.affiliate.stats.sale',[
                'sales' =>$this->getCount()
        ]);
    }

    public function getCount()
    {
        $query = AffiliateSale::query();

        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }
        
        return $query->count();
    }
}
