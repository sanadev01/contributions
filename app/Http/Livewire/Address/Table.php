<?php

namespace App\Http\Livewire\Address;

use App\Repositories\AddressRepository;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $pageSize = 50;
    
    public $user;
    public $name;
    public $address;
    public $phone;

    public $sortBy = 'id';
    public $sortAsc = false;

    public function render()
    {
        return view('livewire.address.table',[
            'addresses' => $this->getAddresses()
            ]);
    }

    public function sortBy($name)
    {
        if ($name == $this->sortBy) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortBy = $name;
        }
    }

    public function getAddresses()
    {
        return (new AddressRepository)->get(request()->merge([
            'user' => $this->user,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone
        ]),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
    }

    public function updating()
    {
        $this->resetPage();
    }


}
