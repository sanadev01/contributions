<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GSSRateRequest extends FormRequest
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
            'country_id'=>'required|exists:countries,id',
            'shipping_service_id'=>'required|exists:shipping_services,id',
            'api_discount'=>'required|min:1',
            'user_discount'=>'required|gt:api_discount', 
            'user_id'=>'required|exists:users,id',
        ];
    }
}
