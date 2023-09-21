<?php

namespace App\Http\Requests\Calculator;

use App\Models\Country;
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
            'recipient_state' => 'required|exists:states,code',
            'recipient_address' => 'required',
            'recipient_city' => 'required',
            'recipient_zipcode' => 'required',
            'recipient_phone' => ($this->has('to_international') || $this->has('from_herco')) ? 'required' : 'sometimes',
            'recipient_first_name' => ($this->has('to_international') || $this->has('from_herco')) ? 'required' : 'sometimes',
            'recipient_last_name' => ($this->has('to_international') || $this->has('from_herco')) ? 'required' : 'sometimes',
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
            'recipient_state.required' => 'Recipient state is required',
            'recipient_state.exists' => 'Recipient state does not exist',
            'recipient_address.required' => 'Recipient address is required',
            'recipient_city.required' => 'Recipient city is required',
            'recipient_zipcode.required' => 'Recipient zipcode is required',
            'recipient_phone.required' => 'Recipient phone is required',
            'recipient_first_name.required' => 'Recipient first name is required',
            'recipient_last_name.required' => 'Recipient last name is required',
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

    public function prepareForValidation()
    {
        if ($this->has('from_herco')) {
            $this->merge([
                'origin_country' => Country::US,
                'sender_state' => 'FL',
                'sender_address' => '2200 NW 129TH AVE',
                'sender_city' => 'MIAMI',
                'sender_zipcode' => '33182',
                'destination_country' => Country::US,
                'recipient_state' => $this->us_recipient_state,
                'from_herco' => true,
            ]);
        }

        if ($this->has('to_herco')) {
            $this->merge([
                'destination_country' => Country::US,
                'recipient_state' => 'FL',
                'recipient_address' => '2200 NW 129TH AVE',
                'recipient_city' => 'MIAMI',
                'recipient_zipcode' => '33182',
                'to_herco' => true,
            ]);
        }

        if ($this->has('from_herco') || $this->has('to_international')) {
            $this->merge([
                'recipient_phone' => $this->phone,
            ]);
        }
    }
}
