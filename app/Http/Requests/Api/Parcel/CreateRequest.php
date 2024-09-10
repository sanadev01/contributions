<?php

namespace App\Http\Requests\Api\Parcel;

use App\Models\Order;
use App\Rules\NcmValidator;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Concerns\HasJsonResponse;
use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
            "parcel.tax_modality" => "in:ddu,DDU,ddp,DDP",
            'parcel.tracking_id' => 'required|max:22',
            'parcel.customer_reference' => 'required|max:22',
            "parcel.measurement_unit" => "required|in:kg/cm,lbs/in",
            
            "parcel.length" => "required|numeric|gt:0",
            "parcel.width" => "required|numeric|gt:0",
            "parcel.height" => "required|numeric|gt:0",
            "parcel.shipment_value" => "nullable|numeric",
            'parcel.return_option' => 'nullable|in:1',

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
                new NcmValidator()
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
            $rules["parcel.weight"] = "required|numeric|gt:0|max:60";
        }else{
            $rules["parcel.weight"] = "required|numeric|gt:0|max:132.28";
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
        if ($shippingService){
            if ($shippingService->is_sweden_post){
                $limit = 60;
            } else if ($shippingService->is_geps){
                $limit = 50;
            } else if ($shippingService->is_correios){
                $limit = 500;
            } else {
                $limit = 200;
            }
            $rules['products.*.description'] = 'required|string|max:' . $limit;
        }



        if ($request->recipient['country_id'] == 'BR' || $request->recipient['country_id'] == 30) {
            $rules['recipient.phone'] = 'required|string|regex:/^\+55\d{8,12}$/';
        }

        if ($shippingService && $shippingService->is_total_express) {

            $rules['products.*.description'] = 'required|max:60';
            $rules['parcel.tax_modality'] = 'required|in:ddu,DDU,ddp,DDP';
        }

        if ($request->recipient['country_id'] == 'UK' || $request->recipient['country_id'] == Country::UK) {
            $rules['recipient.state_id'] = 'nullable';
            $rules['recipient.tax_id'] = 'nullable';
            $rules['recipient.street_no'] = 'nullable';
        }

        if ($request->recipient['country_id'] == 'MEX' || $request->recipient['country_id'] == 'Mexico' || $request->recipient['country_id'] == Country::Mexico) {
            $rules['recipient.tax_id'] = 'nullable';
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
            'recipient.phone.required' => 'The phone number field is required.',
            'recipient.phone.regex' => 'Please enter a valid phone number in international format. Example: +551234567890',
            'parcel.return_option.required' => 'The return option is required. It can be only 1.',
        ];
    }
}
