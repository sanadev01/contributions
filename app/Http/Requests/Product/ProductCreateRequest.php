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
            'order' => 'nullable',
            'category' => 'required',
            'brand' => 'required',
            'manufacturer' => 'required',
            'barcode' => 'required',
            'item' => 'nullable',
            'lot' => 'nullable',
            'unit' => 'nullable',
            'case' => 'nullable',
            'min_quantity' => 'required',
            'max_quantity' => 'required',
            'discontinued' => 'required',
            'store_day' => 'nullable',
            'location' => 'required',
            'sh_code' => 'required',
            'weight' => 'required|numeric|gt:0',
            'measurement_unit' => 'required|in:kg/cm,lbs/in',
        ];

        if ($this->method() == 'POST') {
            $rules['sku'] = 'required|unique:products';
        }

        if ( $this->user()->isAdmin() ){
            $rules['user_id'] = 'required|exists:users,id';
        }

        return $rules;

    }
}
