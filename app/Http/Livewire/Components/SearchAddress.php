<?php

namespace App\Http\Livewire\Components;

use App\Models\User;
use App\Models\State;
use App\Models\Address;
use App\Models\Country;
use Livewire\Component;

class SearchAddress extends Component
{
    public $search;
    public $typeInternational = false;
    public $userId;
    public $addresses;
    public $fromCalculator = false;

    public $phoneNumberEmitted = false;

    protected $listeners = ['address-type' => 'addressType'];

    public function mount($user_id = null, $from_calculator = false)
    {
        $this->userId = ($user_id) ? $user_id : auth()->user()->id;
        $this->fromCalculator = $from_calculator;
    }

    public function render()
    {
        return view('livewire.components.search-address');
    }

    public function addressType($type)
    {
        if ($type == 'international') {
            $this->typeInternational = true;
        } else {
            $this->typeInternational = false;
        }
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

        if (!$value && $this->addresses) {
            $this->addresses = null;
        }
        
    }

    public function selectAddress($address)
    {
        $this->emit('searchedAddress',$address);
        if ($this->fromCalculator) {
            $this->dispatchBrowserEvent('address-searched', ['data' => $this->transformAddress($address)]);
        }
        $this->search = $address['phone'];
        $this->addresses = null;
    }

    private function getAddresses()
    {
        $query = Address::query();

        $query->where('phone', 'LIKE', "%{$this->search}%");

        if ($this->userId != User::ROLE_ADMIN) {
            $query->where('user_id', $this->userId);
        }

        if (!$this->typeInternational) {
            $query->where('country_id', Country::US);
        }

        if ($this->typeInternational) {
            $query->where('country_id', '!=', Country::US);
        }

        $this->addresses = $query->take(5)->get();
    }

    private function transformAddress($address)
    {
        return [
            'id' => $address['id'],
            'state_id' => $address['state_id'],
            'country_id' => $address['country_id'],
            'state_code' => State::find($address['state_id'])->code,
            'country_code' => Country::find($address['country_id'])->code,
            'city' => $address['city'],
            'address' => $address['address'],
            'first_name' => $address['first_name'],
            'last_name' => $address['last_name'],
            'phone' => $address['phone'],
            'email' => $address['email'],
            'zip_code' => $address['zipcode'],
            'typeInternational' => $this->typeInternational,
        ];
    }
}
