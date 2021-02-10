<?php

namespace App\Http\Livewire\Address;

use App\Repositories\AddressRepository;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pageSize = 50;
    
    public $user;
    public $name;
    public $address;
    public $phone;
    public $streetNo;
    public $city;
    public $state;

    public $sortBy = 'first_name';
    public $sortAsc = true;

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
            'phone' => $this->phone,
            'street_no' => $this->streetNo,
            'city' => $this->city,
            'state' => $this->state,
        ]),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
    }

    public function updating()
    {
        $this->resetPage();
    }


}
