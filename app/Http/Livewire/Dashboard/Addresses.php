<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Repositories\PreAlertRepository;

class Addresses extends Component
{
    public $userId;

    public $selected = [];

    

    public function mount($userId=null, $selected = [])
    {
        $this->userId = Auth::user()->isAdmin() ? $userId : Auth::id();
        $this->selected = $selected;
    }

    public function render()
    {
        $user = User::find(Auth::user()->id);
        return view('livewire.dashboard.addresses',['user' => $user]);
    }

    public function setAddress($id, $type)
    {
        $user = User::find($id);
        if($user && $type === "default") {
            saveSetting('default_address', true, $user->id);
            saveSetting('user_address', false, $user->id);
        } else {
            saveSetting('user_address', true, $user->id);
            saveSetting('default_address', false, $user->id);
        }

    }

}
