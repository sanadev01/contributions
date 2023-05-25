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
        if($this->type)
        session()->flash('alert-success',$this->type.' address Updated');
        return view('livewire.home.select-address',['user' => $this->user]);

    }

    public function setAddress($id, $type)
    {
        $id = $this->user->id;
        dd(saveSetting('DEFAULT_ADDRESS', $type, $id),setting('default_address', null, $id));
        // saveSetting('default_address', null, $id);
        if($type){
            saveSetting('DEFAULT_ADDRESS', $type, $id);
        }

    }

}
