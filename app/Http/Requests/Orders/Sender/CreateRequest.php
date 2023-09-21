<?php

namespace App\Http\Requests\Orders\Sender;

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
        return [
            'first_name' => 'required|max:100',
            'last_name' => 'max:100',
            'email' => 'nullable|max:100|email',
            'phone' => 'nullable|max:15',
            'sender_address' => 'sometimes|required',
            'sender_city' => 'sometimes|required',
            'sender_country_id' => 'sometimes|required|integer|exists:countries,id',
            'sender_state_id' => 'bail|required_if:sender_country_id,==,250',
            'sender_zipcode' => 'bail|required_if:sender_country_id,==,250',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'first name required',
            'sender_country_id.required' => 'sender country must be selected',
            'sender_country_id.integer' => 'sender country must be an integer',
            'sender_country_id.exists' => 'sender country does not exist',
        ];
    }
}
