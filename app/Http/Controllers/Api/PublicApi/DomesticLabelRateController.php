<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Calculator\USCalculatorRepository;

class DomesticLabelRateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, USCalculatorRepository $usCalculatorRepository)
    {
        $validated = $request->validate([
            'weight' => 'required',
            'unit' => 'required',
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
            'sender_address' => 'required',
            'sender_city' => 'required',
            'sender_zipcode' => 'required',
            'sender_state' => 'required',
            'origin_country' => 'required',
            'recipient_address' => 'required',
            'recipient_city' => 'required',
            'recipient_zipcode' => 'required',
            'recipient_state' => 'required',
            'destination_country' => 'required',
        ]);
                
        if (!is_numeric($request->destination_country)){
            
            $country = Country::where('code', $request->destination_country)->orwhere('id', $request->destination_country)->first();
            $request->merge(['destination_country' => $country->id]);

        }
        if (!is_numeric($request->sender_country)){
            
            $country = Country::where('code', $request->origin_country)->orwhere('id', $request->origin_country)->first();
            $request->merge(['origin_country' => $country->id]);

        }

        if($request){

            $tempOrder = $usCalculatorRepository->handle($request);
            $shippingServices = $usCalculatorRepository->getShippingServices();
            $rates = $usCalculatorRepository->getRates();

            return apiResponse(true,"Domestic Services Rates",[
                'rates' => $rates
            ]);

        }
        return apiResponse(false, $error);
    }
}
