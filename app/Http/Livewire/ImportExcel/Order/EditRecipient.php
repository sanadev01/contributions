<?php

namespace App\Http\Livewire\ImportExcel\Order;

use App\Models\Country;
use App\Models\ImportedOrder;
use Livewire\Component;
use App\Rules\ZipCodeValidator;
use App\Rules\PhoneNumberValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EditRecipient extends Component
{
    public $orderId; 
    public $order;
    public $edit;

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

    public function mount($order, $edit= '')
    {
        
        $this->orderId = $order->id;
        $this->edit = $edit;
        $order = ImportedOrder::find($this->orderId);
        
        $this->order = $order;
        $this->recipient = $order->recipient;
        $this->first_name = optional($this->recipient)['first_name'];
        $this->last_name =optional($this->recipient)['last_name'];
        $this->email = optional($this->recipient)['email'];
        $this->phone = optional($this->recipient)['phone'];
        $this->address = optional($this->recipient)['address'];
        $this->address2 = optional($this->recipient)['address2'];
        $this->street_no = optional($this->recipient)['street_no'];
        $this->country_id = optional($this->recipient)['country_id'];
        $this->state_id = optional($this->recipient)['state_id'];
        $this->city = optional($this->recipient)['city'];
        $this->zipcode = $this->recipient['zipcode'];
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

    public function data()
    {
        $data =[
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
            'tax_id' => $this->tax_id,
        ];
        return $data;
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
                'required','min:13','max:15', new PhoneNumberValidator($this->country_id)
            ],
            'state_id' => 'required|exists:states,id',
            'zipcode' => [
                'required', new ZipCodeValidator($this->country_id,$this->state_id)
            ]
        ];
        
        if (Country::where('code', 'BR')->first()->id == $this->country_id) {
            $rules['tax_id'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        $message = [
            'first_name.required' => 'first Name is required',
            'last_name.required' => 'last Name is required',
            'email.nullable' => 'email is not valid',
            'phone.required' => 'phone is required',
            'phone.min' => 'The phone must be at least 13 characters.',
            'phone.*.required' => 'Number should be in Brazil International Format',
            'phone.max' => 'The phone may not be greater than 15 characters.',
            'address.required' => 'address is required',
            'address2.nullable' => 'Address2 is not more then 50 character',
            'address2.*.nullable' => 'The address2 may not be greater than 50 characters.',
            'street_no.required' => 'house street no is required',
            'city.required' => 'city is required',
            'state_id.required' => 'state id is required',
            'country_id.required' => 'country is required',
            'zipcode.required' => 'zipcode is required',
            'tax_id.required' => 'The selected recipient tax id is invalid.',
            'tax_id.*.required' => 'The recipient tax id field is required.',
        ];

        if(app()->getLocale() == 'pt'){
            $message['phone.*.required'] = 'O número deve estar no formato internacional do Brazil';
        }

        return $message;
    }
}
