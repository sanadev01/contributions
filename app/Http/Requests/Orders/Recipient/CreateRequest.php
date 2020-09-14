<?php

namespace App\Http\Requests\Orders\Recipient;

use App\Models\Country;
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
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'street_no' => 'required',
            'country_id' => 'required',
            'city' => 'required',
            'phone' => 'required',
            'state_id' => 'required',
        ];

        if (Country::where('code', 'BR')->first()->id == $this->country_id) {
            $rules['cpf'] = 'sometimes|cpf|required_if:country_id,'.Country::where('code', 'BR')->first()->id;
            $rules['cnpj'] = 'sometimes|cnpj|required_if:country_id,'.Country::where('code', 'BR')->first()->id;
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
