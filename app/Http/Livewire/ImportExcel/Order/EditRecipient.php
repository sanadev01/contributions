<?php

namespace App\Http\Livewire\ImportExcel\Order;

use App\Models\Country;
use App\Rules\PhoneNumberValidator;
use App\Rules\ZipCodeValidator;
use Livewire\Component;

class EditRecipient extends Component
{
    public $orderId; 
    public $order;

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

    public function mount($order)
    {
        $this->orderId = $order;

        $this->order = $order;
        $this->recipient = $order->recipient;
        $this->first_name = optional($this->recipient)['first_name'];
        $this->last_name = optional($this->recipient)['last_name'];
        $this->email = optional($this->recipient)['email'];
        $this->phone = optional($this->recipient)['phone'];
        $this->address = optional($this->recipient)['address'];
        $this->address2 = optional($this->recipient)['address2'];
        $this->street_no = optional($this->recipient)['street_no'];
        $this->country_id = optional($this->recipient)['country_id'];
        $this->state_id = optional($this->recipient)['state_id'];
        $this->city = optional($this->recipient)['city'];
        $this->zipcode = optional($this->recipient)['zipcode'];
        $this->tax_id = optional($this->recipient)['tax_id'];
        
    }

    public function render()
    {
        return view('livewire.import-excel.order.edit-recipient');
    }

    public function save()
    {
        $data = $this->validate($this->rules(), $this->messages());

        $error = $this->order->error;
        
        if($error){
            $remainError = array_diff($error, $this->messages());
            $error = $remainError ? $remainError : null;
        }

        $this->order->update([
            'recipient' => $data,
            'error' => $error,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
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
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'first_name.required' => 'first Name is required',
            'last_name.required' => 'last Name is required',
            'email.nullable' => 'email is not valid',
            'phone.required' => 'phone is required',
            'address.required' => 'address is required',
            'address2.nullable' => 'Address2 is not more then 50 character',
            'street_no.required' => 'house street no is required',
            'city.required' => 'city is required',
            'state_id.required' => 'state id is required',
            'country_id.required' => 'country is required',
            'zipcode.required' => 'zipcode is required',
            'tax_id.required' => 'The selected recipient tax id is invalid.',
        ];
    }
}
