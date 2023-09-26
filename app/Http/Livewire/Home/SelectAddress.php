<?php

namespace App\Http\Livewire\Home;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
class SelectAddress extends Component
{
    public $user;
    public $type;    

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function render()
    {
        return view('livewire.home.select-address',['user' => $this->user]);
    }

    public function setAddress($type)
    {
        $id = $this->user->id;
        if(setting('default_address', null, $id) != $type && $type != 1){
            saveSetting('default_address', $type, $id);
        }else{
            saveSetting('default_address', 1, $id);
        }
    }

}
