<?php

namespace App\Http\Livewire\Affiliate;

use App\Models\User;
use Livewire\Component;

class UserCommission extends Component
{

    public $user_id;
    public $ucs;
    public $commission;

    public function mount($userId)
    {
        $this->user_id = $userId;
    }

    public function render()
    {
        return view('livewire.affiliate.user-commission',[
            'users' => User::where('reffered_by', $this->user_id)->get()
        ]);
    }
}
