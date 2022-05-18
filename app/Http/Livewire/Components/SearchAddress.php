<?php

namespace App\Http\Livewire\Components;

use App\Models\Address;
use App\Models\Country;
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

        if (!$value && count($this->addresses) > 0) {
            $this->addresses = null;
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
        $this->addresses = Address::where([
                                            ['user_id', $this->userId],
                                            ['country_id', Country::US],
                                            ['phone', 'LIKE',"%{$this->search}%"]
                                        ])->take(5)->get(
                                            ['id','state_id','first_name','last_name',
                                                'phone','city','address','zipcode'
                                            ]
                                        );
    }
}
