<?php

namespace App\Http\Requests\Orders\OrderDetails;

use App\Rules\NcmValidator;
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
            'shipping_service_id' => 'required|exists:shipping_services,id',
            'items' => 'required|array|min:1',
            'tax_modality' => 'required|in:ddu',
            'items.*.sh_code' => [
                'required',
                'numeric',
                new NcmValidator()
            ], 
            'items.*.description' => 'required|max:190', 
            'items.*.quantity' => 'required|gt:0', 
            'items.*.value' => 'required|gt:0', 
            'items.*.dangrous_item' => 'required', 
        ];
    }

    public function messages()
    {
        return [
            'items.*.sh_code.*' => __('validation.ncm.invalid')
        ];
    }
}
