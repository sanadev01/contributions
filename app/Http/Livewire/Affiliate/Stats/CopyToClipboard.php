<?php

namespace App\Http\Livewire\Affiliate\Stats;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CopyToClipboard extends Component
{
    public $user;
    public $reffer_code;

    public function mount()
    {
        $this->reffer_code = Auth::user()->getRefferCode();
    }
    public function render()
    {
        return view('livewire.affiliate.stats.copy-to-clipboard');
    }
}
