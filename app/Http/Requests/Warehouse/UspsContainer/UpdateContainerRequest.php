<?php

namespace App\Http\Requests\Warehouse\UspsContainer;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContainerRequest extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'unit_type' => 'required|integer|in:1,2',
            'origin_operator_name' => 'sometimes',
            'destination_operator_name' => 'required|in:MIA',
            'seal_no' => 'required|unique:containers,seal_no,'.$request->input('id'),
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
