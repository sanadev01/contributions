<?php

namespace App\Http\Requests\Warehouse\Container;

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
            'destination_operator_name' => 'required|in:SAOD,CRBA,LRD,MEX',
            'services_subclass_code' => 'required',
            'seal_no' => 'required|unique:containers,seal_no',
        ];
    }

    public function messages()
    {
        return [
            'unit_type.*' => __('warehouse.containers.validations.Container Type'),
            'destination_operator_name.*' => __('warehouse.containers.validations.Destination Airport'),
            'services_subclass_code.*' => __('warehouse.containers.validations.Distribution Service Class'),
        ];
    }
}
