<?php

namespace App\Http\Requests\Tax;

use Illuminate\Foundation\Http\FormRequest;

class TaxRequest extends FormRequest
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
            'order_id' => 'required',
            'selling_br' => 'required', 
            'buying_br' => 'required', 
            'selling_br.*' => 'required|numeric|gt:0', 
            'buying_br.*' => 'required|numeric|gt:0',
        ];
    }
    public function messages()
    {
        return [
            'order_id.required'=> 'No order Found!',
            'selling_br.required'=> 'selling rate is required!',
            'buying_br.required'=> 'buying rate is required!',
            'selling_br.*.gt'=> 'selling  rate must be greater than 0.',
            'buying_br.*.gt'=> 'buying rate must be greater than 0.', 
        ];
    }    
}
