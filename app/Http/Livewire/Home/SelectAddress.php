<?php

namespace App\Http\Livewire\Home;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
class SelectAddress extends Component
{
    
    
    public $userId;
    public $type;

    public $selected = [];

    

    public function mount($userId=null, $selected = [])
    {
        $this->userId = Auth::user()->isAdmin() ? $userId : Auth::id();
        $this->selected = $selected;
    }

    public function render()
    {
        $user = User::find(Auth::user()->id); 
        if($this->type)
        session()->flash('alert-success',$this->type.' address Updated');
        return view('livewire.home.select-address',['user' => $user]);

    }

    public function setAddress($id, $type)
    {
        $user = User::find($id);
        $this->type = $type;
        if($user && $type === "default") {
            
            saveSetting('default_address', true, $user->id);
            saveSetting('user_address', false, $user->id);
             
        } else {
            saveSetting('user_address', true, $user->id);
            saveSetting('default_address', false, $user->id);
        }

    }

}
