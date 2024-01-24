<?php

namespace App\Http\Requests\Orders\OrderDetails;
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
         $rules = [
            'customer_reference' => ($this->order->recipient->country_id == \App\Models\Order::CHILE) ? 'required' : 'nullable',
            'shipping_service_id' => 'required|exists:shipping_services,id', 
            'tax_modality' => 'required|in:ddu,ddp', 
        ];
        
        return $rules;
        
    }

    public function messages()
    {
        return [ 
        ];
    }

    public function prepareForValidation()
    {
        if ($this->filled('user_declared_freight')) {
            $this->merge([
                'user_declared_freight' => (float)$this->user_declared_freight
            ]);
        }
    }
}
