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
            'first_name' => 'required|max:50',
            'last_name' => 'nullable|max:50',
            'address' => 'required',
            'address2' => 'nullable|max:50',
            'street_no' => 'sometimes',
            'country_id' => 'required|exists:countries,id',
            'city' => 'required',
            'phone' => [
                'required','max:15','min:11', new PhoneNumberValidator($this->country_id)
            ],
            'state_id' => 'sometimes|required|exists:states,id',
            'region' => 'sometimes|required',
            'zipcode' => [
                'required'
                // 'required',  new CorreosAddresstValidator($this->country_id,$this->address), new ZipCodeValidator($this->country_id,$this->state_id)
            ]
        ];

        if (Country::where('code', 'BR')->first()->id == $this->country_id) {
            $rules['cpf'] = 'sometimes|cpf|required_if:country_id,'.Country::where('code', 'BR')->first()->id;
            $rules['cnpj'] = 'sometimes|cnpj|required_if:country_id,'.Country::where('code', 'BR')->first()->id;
            $rules['zipcode'] = ['required', new ZipCodeValidator($this->country_id,$this->state_id)];
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
