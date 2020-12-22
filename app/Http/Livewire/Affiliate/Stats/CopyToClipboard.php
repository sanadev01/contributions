<?php

namespace App\Http\Livewire\Affiliate\Stats;

use App\Models\User;
use Livewire\Component;

class CopyToClipboard extends Component
{
    public $user;

    public function mount()
    {
        
        if(!auth()->user()->reffer_code){
            User::getRefferCode();
        }
    }
    public function render()
    {
        return view('livewire.affiliate.stats.copy-to-clipboard',[
            'reffer_code' => User::find(auth()->id())->reffer_code
        ]);
    }
}
