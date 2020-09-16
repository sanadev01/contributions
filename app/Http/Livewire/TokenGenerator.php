<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Models\User;

class TokenGenerator extends Component
{   
    
    public $userId;

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    public function render()
    {
        return view('livewire.token-generator',[
            'user' => User::find($this->userId)
        ]);
    }

    public function revoke()
    {
        User::find($this->userId)->update([
            'api_token' => md5(microtime()).'-'.Str::random(116).'-'.md5(microtime())
        ]);
    }

}
