<?php

namespace App\Http\Requests\Admin\Service;

use Illuminate\Foundation\Http\FormRequest;

class CreateShippingService extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return auth()->user()->isAdmin();
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
            'name' => 'required',
            'max_length_allowed' => 'required|numeric',
            'max_width_allowed' => 'required|numeric',
            'min_width_allowed' => 'required|numeric',
            'min_length_allowed' => 'required|numeric',
            'max_sum_of_all_sides' => 'required|numeric',
            'max_weight_allowed' => 'required|numeric',
            'contains_battery_charges' => 'required|numeric',
            'contains_perfume_charges' => 'required|numeric',
            'contains_flammable_liquid_charges' => 'required|numeric',
        ];
    }
}
