<?php

namespace App\Http\Requests\Calculator;

use Illuminate\Foundation\Http\FormRequest;

class USCalculatorRequest extends FormRequest
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
            'origin_country' => 'required|numeric|exists:countries,id',
            'destination_country' => 'required|numeric|exists:countries,id',
            'sender_state' => 'required|exists:states,code',
            'sender_address' => 'required',
            'sender_city' => 'required',
            'sender_zipcode' => 'required',
            'height' => 'sometimes|numeric',
            'width' => 'sometimes|numeric',
            'length' => 'sometimes|numeric',
            'unit' => 'required|in:lbs/in,kg/cm',
            'weight' => ($this->unit == 'kg/cm') ? 'sometimes|numeric|max:30' : 'sometimes|numeric|max:66.15',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */

    public function messages()
    {
        return [
            'origin_country.required' => 'Origin country is required',
            'origin_country.numeric' => 'Origin country must be numeric',
            'origin_country.exists' => 'Origin country does not exist',
            'destination_country.required' => 'Destination country is required',
            'destination_country.numeric' => 'Destination country must be numeric',
            'destination_country.exists' => 'Destination country does not exist',
            'sender_state.required' => 'Sender state is required',
            'sender_state.exists' => 'Sender state does not exist',
            'sender_address.required' => 'Sender address is required',
            'sender_city.required' => 'Sender city is required',
            'sender_zipcode.required' => 'Sender zipcode is required',
            'height.numeric' => 'Height must be numeric',
            'width.numeric' => 'Width must be numeric',
            'length.numeric' => 'Length must be numeric',
            'unit.required' => 'Unit is required',
            'unit.in' => 'Unit must be in lbs/in or kg/cm',
            'weight' => 'Please Enter weight',
            'weight.numeric' => 'Weight must be numeric',
            'weight.max' => 'weight exceed the delivery of UPS',
        ];
    }
}
