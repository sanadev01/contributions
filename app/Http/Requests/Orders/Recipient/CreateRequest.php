<?php

namespace App\Http\Requests\Orders\Recipient;

use App\Models\Country;
use App\Rules\ZipCodeValidator;
use App\Rules\PhoneNumberValidator;
use App\Rules\CorreosAddresstValidator;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [ 
            'first_name' => ($this->country_id == Country::Chile) ? 'required|max:28' : 'required|max:50',
            'address' => 'required',
            'address2' => 'nullable|max:50',
            'street_no' => 'sometimes',
            'country_id' => 'required|exists:countries,id',
            'city' => ($this->country_id == \App\Models\Order::Guatemala)?'nullable': 'required_if:service,==,postal_service',
            'commune_id' => 'required_if:service,==,courier_express',
            'phone' => [
                (($this->country_id == \App\Models\Order::Guatemala)?'nullable':'required'),'max:15','min:11', new PhoneNumberValidator($this->country_id)
            ],
            'state_id' => 'sometimes|required|exists:states,id',
            'region' => ['sometimes', ($this->country_id == \App\Models\Order::Guatemala)?'nullable':'required'],
            'zipcode' => [
                ($this->country_id == \App\Models\Order::Guatemala)?'nullable': 'required'
                // 'required',  new CorreosAddresstValidator($this->country_id,$this->address), new ZipCodeValidator($this->country_id,$this->state_id)
            ]
        ];

        // if (Country::where('code', 'BR')->first()->id == $this->country_id) {
        //     $rules['cpf'] = 'sometimes|cpf|required_if:country_id,'.Country::where('code', 'BR')->first()->id;
        //     $rules['cnpj'] = 'sometimes|cnpj|required_if:country_id,'.Country::where('code', 'BR')->first()->id;
        //     $rules['zipcode'] = ['required', new ZipCodeValidator($this->country_id,$this->state_id)];
        // }
        if (Country::where('code', 'BR')->first()->id == $this->country_id && $this->account_type =='individual') {
            $rules['tax_id'] = 'sometimes|cpf_cnpj|required_if:country_id,'.Country::where('code', 'BR')->first()->id;
            // $rules['tax_id'] = 'sometimes|cnpj|required_if:country_id,'.Country::where('code', 'BR')->first()->id;
            $rules['zipcode'] = [ ($this->country_id == \App\Models\Order::Guatemala ?'nullable':'required'), new ZipCodeValidator($this->country_id,$this->state_id)];
            $rules['street_no'] = 'sometimes|numeric';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'cpf.cpf' => 'Invalid CPF',
            'cpf.*' => 'CPF Required For Brazil',
            'cnpj.cnpj' => 'Invalid CNPJ',
            'cnpj.*' => 'CNPJ Required for Brazil',
            'country_id.*' => 'Select A country From List',
            'phone.phone' => 'Invalid Phone number'
        ];
    }
}
