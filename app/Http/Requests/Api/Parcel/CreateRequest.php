<?php

namespace App\Http\Requests\Api\Parcel;

use App\Http\Requests\Concerns\HasJsonResponse;
use App\Rules\NcmValidator;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    use HasJsonResponse;
    
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
            "parcel.service_id" => "required|exists:shipping_services,id",
            "parcel.merchant" => "required",
            "parcel.carrier" => "required",
            "parcel.tracking_id" => "sometimes|unique:orders,tracking_id",
            "parcel.customer_reference" => "unique:orders,customer_reference",
            "parcel.measurement_unit" => "required|in:kg/cm,lbs/in",
            "parcel.weight" => "required|numeric|gt:0|max:30",
            "parcel.length" => "required|numeric|gt:0",
            "parcel.width" => "required|numeric|gt:0",
            "parcel.height" => "required|numeric|gt:0",

            "sender.sender_first_name" => "required|max:100",
            "sender.sender_last_name" => "required|max:100",
            "sender.sender_email" => "required|email",
            "sender.sender_taxId" => "required",

            "recipient.first_name" => "required",
            "recipient.last_name" => "required",
            "recipient.email" => "required|email",
            "recipient.phone" => "required",
            "recipient.city" => "required",
            "recipient.street_no" => "required",
            "recipient.address" => "required",
            "recipient.address2" => "sometimes|max:50",
            "recipient.account_type" => "required|in:individual,business",
            "recipient.tax_id" => "required",
            "recipient.zipcode" => "required",
            "recipient.state_id" => "required|exists:states,id",
            "recipient.country_id" => "required|exists:countries,id",

            "products" => "required|array|min:1",

            "products.*.sh_code" => [
                "required",
                new NcmValidator()
            ],
            "products.*.description" => "required",
            "products.*.quantity" => "required|min:1",
            "products.*.value" => "required|gt:0",
            "products.*.is_battery" => "required|in:0,1",
            "products.*.is_perfume" => "required|in:0,1",
            "products.*.is_flameable" => "required|in:0,1",
        ];
    }

    public function messages()
    {
        return [
            "products.*.sh_code.*" => __('validation.ncm.invalid')." (:input)"
        ];
    }
}
