<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
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
            'name' => 'required',
            'price' => 'required',
            'sku' => 'required',
            'description' => 'required',
            'quantity' => 'required|numeric|gt:0',
            'order' => 'required',
            'category' => 'required',
            'brand' => 'required',
            'manufacturer' => 'required',
            'barcode' => 'required',
            'item' => 'required',
            'lot' => 'required',
            'unit' => 'required',
            'case' => 'required',
            'inventory_value' => 'required',
            'min_quantity' => 'required',
            'max_quantity' => 'required',
            'discontinued' => 'required',
            'store_day' => 'required',
            'location' => 'required',
            'sh_code' => 'required',

        ];

        if ( $this->user()->isAdmin() ){
            $rules['user_id'] = 'required|exists:users,id';
        }

        return $rules;

    }
}
