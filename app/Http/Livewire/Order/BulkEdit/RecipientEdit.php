<?php

namespace App\Http\Livewire\Order\BulkEdit;

use App\Models\Country;
use App\Models\Recipient;
use App\Rules\PhoneNumberValidator;
use App\Rules\ZipCodeValidator;
use Livewire\Component;

class RecipientEdit extends Component
{
    public $recipient;

    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $address;
    public $address2;
    public $street_no;
    public $country_id;
    public $state_id;
    public $city;
    public $zipcode;
    public $tax_id;

    public function mount(Recipient $recipient)
    {
        $this->recipient = $recipient;
    }


    public function render()
    {
        $this->first_name = $this->recipient->first_name;
        $this->last_name = $this->recipient->last_name;
        $this->email = $this->recipient->email;
        $this->phone = $this->recipient->phone;
        $this->address = $this->recipient->address;
        $this->address2 = $this->recipient->address2;
        $this->street_no = $this->recipient->street_no;
        $this->country_id = $this->recipient->country_id;
        $this->state_id = $this->recipient->state_id;
        $this->city = $this->recipient->city;
        $this->zipcode = $this->recipient->zipcode;
        $this->tax_id = $this->recipient->tax_id;

        return view('livewire.order.bulk-edit.recipient-edit');
    }

    public function save()
    {
        $this->initializeRules();
        $this->validate();

        $this->recipient->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'address2' => $this->address2,
            'street_no' => $this->street_no,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city' => $this->city,
            'zipcode' => $this->zipcode,
            'tax_id' => $this->tax_id
        ]);
    }

    public function initializeRules()
    {
        $this->rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'nullable|max:50',
            'address' => 'required',
            'address2' => 'nullable|max:50',
            'street_no' => 'required',
            'country_id' => 'required|exists:countries,id',
            'city' => 'required',
            'phone' => [
                'required','max:15','min:13', new PhoneNumberValidator($this->country_id)
            ],
            'state_id' => 'required|exists:states,id',
            'zipcode' => [
                'required', new ZipCodeValidator($this->country_id,$this->state_id)
            ]
        ];

        if (Country::where('code', 'BR')->first()->id == $this->country_id) {
            $rules['cpf'] = 'sometimes|cpf|required_if:country_id,'.Country::where('code', 'BR')->first()->id;
            $rules['cnpj'] = 'sometimes|cnpj|required_if:country_id,'.Country::where('code', 'BR')->first()->id;
        }
    }
}
