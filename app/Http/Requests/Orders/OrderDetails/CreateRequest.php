<?php

namespace App\Http\Requests\Orders\OrderDetails;

use App\Rules\NcmValidator;
use App\Models\ShippingService;
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
            'items' => 'required|array|min:1',
            'tax_modality' => 'required|in:ddu',
            'items.*.sh_code' => ($this->order->products->isNotEmpty()) ? 'sometimes' : [
                'required',
                'numeric',
            ], 
            'items.*.description' => 'required|max:200', 
            'items.*.quantity' => 'required|gt:0', 
            'items.*.value' => 'required|gt:0', 
            'items.*.dangrous_item' => 'required', 
        ];

        $shippingService = ShippingService::find($this->shipping_service_id ?? null);

        if($shippingService && $shippingService->isPostNLService()) {
            $rules['items.*.description'] = 'required|max:45';
        }
        
        return $rules;
        
    }

    public function messages()
    {
        return [
            'items.*.sh_code.*' => __('validation.ncm.invalid'),
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
