<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\USLabelRepository;
use App\Repositories\DomesticLabelRepository;
use App\Repositories\Api\DomesticRateRepository;
use App\Repositories\ConsolidateDomesticLabelRepository;

class DomesticLabelController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, ConsolidateDomesticLabelRepository $consolidatedDomesticLabelRepository, USLabelRepository $usLabelRepostory, DomesticLabelRepository $domesticLabelRepository, DomesticRateRepository $domesticRateRepository)
    {
        $validated = $request->validate([
            'warehouse_no' => 'required',
            'weight' => 'required',
            'unit' => 'required',
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
            //'service' => 'required',
            'sender_first_name' => 'required',
            'sender_address' => 'required',
            'sender_city' => 'required',
            'sender_zipcode' => 'required',
            'sender_state' => 'required',
            'sender_country' => 'required',
            'recipient' => 'required',
        ]);

        // $trackIds = json_decode(json_encode($validated['tracking_code']), true);
        // $orderIds = Order::whereIn('corrios_tracking_code', $trackIds)->pluck('id');

        if (!$request->warehouse_no) {
            return apiResponse(false,"Please provide order warehouse#.");
        }

        if (!is_numeric($request->recipient['country'])){
            
            $country = Country::where('code', $request->recipient['country'])->orwhere('id', $request->recipient['country'])->first();
            $request->merge(['destination_country' => $country->id]);

        }

        if (!is_numeric($request->sender_country)){
            
            $country = Country::where('code', $request->sender_country)->orwhere('id', $request->sender_country)->first();
            $request->merge(['origin_country' => $country->id]);

        }

        if (!is_numeric($request->recipient)){
            $request->merge(['recipient_address' => $request->recipient['streetLines'], 'recipient_city' => $request->recipient['city'], 'recipient_zipcode' => $request->recipient['postalCode'], 'recipient_state' => $request->recipient['stateOrProvinceCode']]);
        }

        $orders = $consolidatedDomesticLabelRepository->getInternationalOrders($request->warehouse_no);
        if ($orders->isEmpty()) {
            return apiResponse(false,"Selected orders already have domestic label");
        }
        $rates = $domesticRateRepository->domesticServicesRates($request);

        $error = $consolidatedDomesticLabelRepository->getErrors();

        if(!$error && $rates && $request->service){

            $totalWeight = $consolidatedDomesticLabelRepository->getTotalWeight($orders);

            if(request()->unit == 'kg/cm' && request()->weight > $totalWeight['totalWeightInKg'] || request()->unit == 'lbs/in' && request()->weight > $totalWeight['totalWeightInLbs']) {             
                
                $domesticLabelRepository->handle();
                $shippingServices = $domesticLabelRepository->getShippingServices($order);
                $label = $domesticLabelRepository->getDomesticLabel($order);
                return apiResponse(true,"Label Successfully Printed",[
                    'weight' => $totalWeight,
                    'label' => $label
                ]);
            
            }
            
            return apiResponse(false,"The weight provided is invalid.",[
                'error' => "Given weight must be greater than the order weight.",
            ]);

        }
        return apiResponse(false, $error);
    }
}
