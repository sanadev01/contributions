<?php

namespace App\Http\Requests\Consolidation;

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
            'parcels' => 'required|array|min:2',
            'parcels.*' => 'integer|exists:orders,id'
        ];
    }
}
