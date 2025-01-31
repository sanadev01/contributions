<?php

namespace App\Http\Requests\Orders;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class PreAlertUpdateRequest extends FormRequest
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
            'merchant' => 'required',
            'carrier' => 'required',
            'tracking_id' => 'required',
            'order_date' => 'required|before:tomorrow',
        ];

        if ( $this->user()->can('addWarehouseNumber',Order::class) ){
            $rules['whr_number'] = 'required|unique:orders,warehouse_number,'.$this->route('parcel')->id;
        }

        if ( $this->user()->can('addShipmentDetails',Order::class) ){
            $rules['weight'] = 'required|numeric|gt:0';
            $rules['unit'] = 'required|in:kg/cm,lbs/in';
            $rules['length'] = 'required|numeric|gt:0';
            $rules['width'] = 'required|numeric|gt:0';
            $rules['height'] = 'required|numeric|gt:0';
        }

        if ( $this->user()->isAdmin() ){
            $rules['user_id'] = 'required|exists:users,id';
        }

        if($this->hasFile('invoiceFile')){
            $rules['invoiceFile'] = 'required|file';
        }

        return $rules;
    }
}
