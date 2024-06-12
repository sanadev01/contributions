<?php

namespace App\Http\Requests\Admin\ProfitPackage;

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
            'package_name' => 'required|string|max:90',
            'shipping_service_id' => 'required',
            'slab' => 'required|array',
            'slab.*.min_weight' => 'required|numeric',
            'slab.*.max_weight' => 'required|numeric',
            'slab.*.value' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'shipping_service_id' => 'shipping service must be selected',
            'slab.*.min_weight.*' => 'Numeric value required',
            'slab.*.max_weight.*' => 'Numeric value required',
            'slab.*.value.*' => 'Numeric value required',
        ];
    }

}
