<?php

namespace App\Http\Requests\Shared\Profile;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
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
            'name' => 'required|max:100',
            'phone' => 'required|max:15'
        ];

        if (! auth()->user()->isBusinessAccount()) {
            $rules['last_name'] = 'required|max:100';
        }

        // if (auth()->user()->isAdmin()) {
        //     $rules['pobox_address'] = 'required|max:200';
        // }

        if ($this->password) {
            $rules['password'] =[
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'regex:/[@$!%*?&^#()_+={}\[\]|:;,.<>~`]/'
            ];
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
           'phone.phone' => 'Invalid Telefone',
           'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
        ];
    }
}
