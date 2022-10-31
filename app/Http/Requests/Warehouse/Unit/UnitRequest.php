<?php

namespace App\Http\Requests\Warehouse\Unit;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
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
            'type'      => 'required',
            'start_date'=> 'required',
            'end_date'  => 'required',
        ];
        if($this->type == 'departure_info'){
            $rules['unitCode']        = 'required';
            $rules['flightNo']        = 'required';
            $rules['airlineCode']     = 'required';
            $rules['deprAirportCode'] = 'required';
            $rules['arrvAirportCode'] = 'required';
            $rules['destCountryCode'] = 'required';
        }
    }

}
