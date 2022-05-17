<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;

class SearchAddress extends Component
{
    public $search;
    public $userId;
    public $addresses;

    public $phoneNumberEmitted = false;

    public function mount($user_id = null)
    {
        $this->userId = ($user_id) ? $user_id : auth()->user()->id;
    }

    public function render()
    {
        return view('livewire.components.search-address');
    }

    public function updatedSearch($value)
    {
        if (strlen($value) >= 4) {
            $this->getAddresses();
        }

        if (strlen($value) >= 10) {
            $this->phoneNumberEmitted = true;
            $this->emit('phoneNumber',$value);
        }
        if (strlen($value) < 10 && $this->phoneNumberEmitted) {
            $this->emit('phoneNumber', $value);
        }
        
    }

    public function selectAddress($address)
    {
        $this->emit('searchedAddress',$address);
        $this->search = $address['phone'];
        $this->addresses = null;
    }

    private function getAddresses()
    {
        $this->addresses = \App\Models\Address::where([
                                                    ['user_id', $this->userId],
                                                    ['country_id', \App\Models\Country::US],
                                                    ['phone', 'LIKE',"%{$this->search}%"]
                                                ])->take(5)->get(
                                                    ['id','state_id','first_name','last_name',
                                                        'phone','city','address','zipcode'
                                                    ]
                                                );
    }
}
