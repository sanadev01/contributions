<?php

namespace App\Http\Requests\Warehouse\PostnlContainer;

use Illuminate\Foundation\Http\FormRequest;

class CreateContainerRequest extends FormRequest
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
            'unit_type' => 'required|integer|in:1,2',
            'origin_country' => 'required',
            'destination_country' => 'required',
            'services_subclass_code' => 'required',
            'seal_no' => 'required|unique:containers,seal_no',
        ];
    }

    public function messages()
    {
        return [
            'unit_type.*' => __('warehouse.containers.validations.Container Type'),
            'destination_country.*' => __('warehouse.containers.validations.Destination Country'),
            'services_subclass_code.*' => __('warehouse.containers.validations.Distribution Service Class'),
        ];
    }
}
