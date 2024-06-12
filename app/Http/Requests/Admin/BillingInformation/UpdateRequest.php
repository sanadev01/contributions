<?php

namespace App\Http\Requests\Admin\BillingInformation;

use Illuminate\Foundation\Http\FormRequest;
 
class UpdateRequest extends FormRequest
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
        return [
            'expiration' => 'date_format:m/y|after:today',
            'cvv' => 'regex:/(^\d{3})/u',
            'first_name' => 'required', 
            'last_name' => 'required', 
            'address' => 'required', 
            'phone' => 'required', 
            'state' => 'required|exists:states,id', 
            'zipcode' => 'required',
            'country' => 'required|exists:countries,id', 
        ];
    }
}
