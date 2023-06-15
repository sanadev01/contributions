<?php

namespace App\Http\Requests\Api\Parcel;

use App\Models\Order;
use App\Rules\NcmValidator;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Concerns\HasJsonResponse;

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
    public function rules(Request $request)
    {
        $order = Order::where([
                    ['user_id', auth()->user()->id],
                    ['tracking_id', $request->parcel['tracking_id']]
                ])
                ->orWhere([
                    ['user_id', auth()->user()->id],
                    ['customer_reference', $request->parcel['customer_reference']]
                ])
                ->first();
                
        $rules = [
            "parcel.service_id" => "bail|required|exists:shipping_services,id",
            "parcel.merchant" => "required",
            "parcel.carrier" => "required",
            'parcel.tracking_id' => 'required|max:22',
            'parcel.customer_reference' => 'required|max:22',
            "parcel.measurement_unit" => "required|in:kg/cm,lbs/in",
            
            "parcel.length" => "required|numeric|gt:0",
            "parcel.width" => "required|numeric|gt:0",
            "parcel.height" => "required|numeric|gt:0",
            "parcel.shipment_value" => "nullable|numeric",

            "sender.sender_first_name" => "required|max:100",
            "sender.sender_last_name" => "required|max:100",
            "sender.sender_email" => "required|email",
            "sender.sender_taxId" => "nullable",

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
            ],
            "products.*.description" => "required",
            "products.*.quantity" => "required|min:1",
            "products.*.value" => "required|gt:0",
            "products.*.is_battery" => "required|in:0,1",
            "products.*.is_perfume" => "required|in:0,1",
            "products.*.is_flameable" => "required|in:0,1",
        ];

        if ($order) {
            $rules['parcel.tracking_id'] = 'required|unique:orders,tracking_id';
            $rules['parcel.customer_reference'] = 'required|unique:orders,customer_reference';
        }

        if(optional($request->parcel)['measurement_unit'] == 'kg/cm'){
            $rules["parcel.weight"] = "required|numeric|gt:0|max:30";
        }else{
            $rules["parcel.weight"] = "required|numeric|gt:0|max:66.15";
        }
        if (is_numeric( optional($request->recipient)['country_id'])){
            $rules["recipient.country_id"] = "required|exists:countries,id";
        }else{
            $rules["recipient.country_id"] = "required|exists:countries,code";
        }
        if (is_numeric( optional($request->recipient)['state_id'])){
            $rules["recipient.state_id"] = "required|exists:states,id";
        }else{
            $rules["recipient.state_id"] = "required|exists:states,code";
        }

        $shippingService = ShippingService::find($request->parcel['service_id'] ?? null);

        if ($shippingService && $shippingService->isOfUnitedStates()) {

            $rules['sender.sender_country_id'] = 'required';
            $rules['sender.sender_state_id'] = 'required';
            $rules['sender.sender_city'] = 'required|string|max:100';
            $rules['sender.sender_address'] = 'required|string|max:100';
            $rules['sender.sender_phone'] = 'sometimes|string|max:100';
            $rules['sender.sender_zipcode'] = 'required';
            $rules['recipient.phone'] = 'required|string|max:12';
        }

        if ($shippingService && $shippingService->isPostNLService()) {
            $rules['products.*.description'] = 'required|max:45';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            "products.*.sh_code.*" => __('validation.ncm.invalid')." (:input)",
            'sender.sender_address.required_if' => __('validation.sender_address.required_if'),
            'sender.sender_country_id.required_if' => __('validation.sender_country_id.required_if'),
            'sender.sender_state_id.required_if' => __('validation.sender_state_id.required_if'),
            'sender.sender_city.required_if' => __('validation.sender_city.required_if'),
        ];
    }
}
