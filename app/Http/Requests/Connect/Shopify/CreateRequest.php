<?php

namespace App\Http\Requests\Connect\Shopify;

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
            'connect_name' => 'required' ,
            'connect_store_url' => 'required|url' ,
        ];
    }
}
