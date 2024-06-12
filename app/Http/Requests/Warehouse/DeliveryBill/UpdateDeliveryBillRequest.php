<?php

namespace App\Http\Requests\Warehouse\DeliveryBill;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryBillRequest extends FormRequest
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
            'container' => 'required|array|min:1',
            'container.*' => 'required|exists:containers,id|unique:container_delivery_bill,container_id,'.$this->delivery_bill->id.',delivery_bill_id'
        ];
    }

    public function messages()
    {
        return [
            'container.*' => 'Please Select at least 1 container to created Delivery Bill',
            'container.*.*' => 'Please Select at least 1 container to created Delivery Bill',
            'container.*.unique' => 'Container :attribute already added in other delivery bill'
        ];
    }
}
