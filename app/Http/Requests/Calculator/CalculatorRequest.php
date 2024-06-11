<?php

namespace App\Http\Requests\Calculator;

use Illuminate\Foundation\Http\FormRequest;

class CalculatorRequest extends FormRequest
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
            'country_id' => 'required|numeric|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'height' => 'sometimes|numeric',
            'width' => 'sometimes|numeric',
            'length' => 'sometimes|numeric',
            'unit' => 'required|in:lbs/in,kg/cm',
        ];
        if($this->unit == 'kg/cm'){
            $rules['weight'] = 'sometimes|numeric|max:30';
        }else{
            $rules['weight'] = 'sometimes|numeric|max:66.15';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */

    public function messages()
    {
      return  $message = [
            'country_id.required' => 'Please Select A country',
            'state_id.required' => 'Please Select A state',
            'weight' => 'Please Enter weight',
            'weight.max' => 'weight exceed the delivery of Correios',
            'height' => 'Please Enter height',
            'width' => 'Please Enter width',
            'length' => 'Please Enter length',
            'unit' => 'Please Select Measurement Unit ',
        ];
        
    }
}
