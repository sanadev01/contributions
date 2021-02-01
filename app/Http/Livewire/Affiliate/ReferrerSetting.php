<?php

namespace App\Http\Livewire\Affiliate;

use App\Models\User;
use Livewire\Component;

class ReferrerSetting extends Component
{
    public $userId;
    public $referrer_id;

    public function mount($userId)
    {
        $this->userId       = $userId;
        $this->referrer_id  = $this->referrer_id;
    }

    public function render()
    {
        return view('livewire.affiliate.referrer-setting',[
            'users' => User::user()->get()
        ]);
    }

    public function updatedReferrerId(){
        User::find($this->referrer_id)->update([
            'reffered_by' => $this->userId
        ]);
    }

}
