<?php

namespace App\Http\Requests\Admin\Rate;

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
        // return auth()->user()->isAdmin();
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
            'country_id' => 'required',
            'csv_file' => 'required|file|max:15000|mimes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,xlsx',
        ];
    }

    public function messages()
    {
        return [
            'country_id.required' => 'Country Must be Selected',
            'csv_file.required' => 'An Excel File less then 15Mb is required',
            'csv_file.max' => 'An Excel File less then 15Mb is allowed',
            'csv_file.mimes' => 'A Valid Excel File is allowed'
        ];
    }

}
