<?php

namespace App\Http\Requests\Product;

use App\Models\Order;
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
            'name'          => 'required',
            'price'         => 'required',
            'sku'           => 'required',
            'sh_code'       => 'required',
            'description'   => 'required',
            'quantity'      => 'required|numeric|gt:0',
            'merchant'      => 'required',
            'carrier'       => 'required',
            'tracking_id'   => 'required',
            'order_date'    => 'required|before:tomorrow',
        ];
        $rules['whr_number']= 'required|unique:orders,warehouse_number';
        $rules['weight']    = 'required|numeric|gt:0';
        $rules['unit']      = 'required|in:kg/cm,lbs/in';
        $rules['length']    = 'required|numeric|gt:0';
        $rules['width']     = 'required|numeric|gt:0';
        $rules['height']    = 'required|numeric|gt:0';
     

        if ( $this->user()->isAdmin() ){
            $rules['user_id'] = 'required|exists:users,id';
        }

        if($this->hasFile('invoiceFile')){
            $rules['invoiceFile'] = 'required|file';
        }

        return $rules;

    }
}
