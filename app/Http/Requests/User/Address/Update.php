<?php

namespace App\Http\Requests\User\Address;

use App\Models\Country;
use App\Rules\PhoneNumberValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->route('address') && (Auth::user()->isAdmin() || $this->route('address')->user_id == Auth::id());
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
            'address2' => 'max:50',
            'street_no' => 'required',
            'country_id' => 'required|integer',
            'city' => 'required',
            'phone' => [ 'required', new PhoneNumberValidator($this->country_id)],
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
            'cpf.*' => 'CPF Required For Brazil',
            'cpf.cpf' => 'Invalid CPF',
            'cnpj.*' => 'CNPJ Required for Brazil',
            'cnpj.cnpj' => 'Invalid CNPJ',
            'country_id.*' => 'Select A country From List',
            'phone.phone' => 'Invalid Phone number'
        ];
    }
}
