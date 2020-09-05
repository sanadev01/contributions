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
            'first_name' => 'max:255',
            'last_name' => 'max:255',
            'card_no' => 'max:255',
            'expiration' => 'max:255',
            'cvv' => 'max:255',
            'phone' => 'max:255',
            'address' => 'max:255',
            'state' => 'max:255',
            'zipcode' => 'max:255',
            'country' => 'max:255'
        ];
    }
}
