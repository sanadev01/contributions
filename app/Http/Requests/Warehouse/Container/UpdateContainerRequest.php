<?php

namespace App\Http\Requests\Warehouse\Container;

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
    public function rules()
    {
        $id = ($this->route()->getName() == 'warehouse.colombia-containers.update') ? $this->route('colombia_container')->id  
                                                                                    : (($this->route()->getName() == 'warehouse.mile-express-containers.update') 
                                                                                    ? $this->route('mile_express_container')->id : $this->route('container')->id);

        return [
            'unit_type' => 'required|integer|in:1,2',
            'destination_operator_name' => 'required',
            'seal_no' => 'required|unique:containers,seal_no,'.$id,
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
