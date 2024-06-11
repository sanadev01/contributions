<?php

namespace App\Http\Livewire\Components;

use App\Models\User;
use Livewire\Component;

class SearchUser extends Component
{
    public $search;
    public $userId;
    public $usersList;

    public function mount($selectedId= null)
    {
        $user = null;
        if ( $selectedId ){
            $user = User::find($selectedId);
        }

        $this->search = old('user', optional($user)->name );
        $this->userId = old('user_id',$selectedId);
    }

    public function render()
    {
        return view('livewire.components.search-user');
    }

    public function updatedSearch()
    {
        $this->userId = null;
        if ( !$this->search ){
            $this->usersList = [];
            $this->emit('clear-search');
            return;
        }

        $this->usersList = User::query()
            ->user()
            ->where(function($query){
                $query->where('name','LIKE',"%{$this->search}%")
                    ->orWhere('email','LIKE',"%{$this->search}%")
                    ->orWhere('pobox_number','LIKE',"%{$this->search}%")
                    ->orWhere('id','LIKE',"%{$this->search}%");
            })
            ->get()
            ->toArray();
    }

    public function selectUser($userId, $username)
    {
        $this->search = $username;
        $this->userId = $userId;
        $this->usersList = [];

        $this->emit("user:updated",$userId);
    }
}
