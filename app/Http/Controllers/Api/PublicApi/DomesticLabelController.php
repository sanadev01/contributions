<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\USLabelRepository;
use App\Repositories\DomesticLabelRepository;
use App\Repositories\ConsolidateDomesticLabelRepository;

class DomesticLabelController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, ConsolidateDomesticLabelRepository $consolidatedDomesticLabelRepository, USLabelRepository $usLabelRepostory, DomesticLabelRepository $domesticLabelRepository)
    {
        $validated = $request->validate([
            'tracking_code' => 'required',
            'weight' => 'required',
            'unit' => 'required',
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
            'service' => 'required',
            'total_price' => 'required',
            'first_name' => 'required',
            'sender_address' => 'required',
            'sender_city' => 'required',
            'sender_zipcode' => 'required',
            'sender_state' => 'required',
            'recipient' => 'required',
        ]);

        $trackIds = json_decode(json_encode($validated['tracking_code']), true);
        $orderIds = [];
        if (count($trackIds) >= 1) {
            foreach ($trackIds as $key => $item) {
                $trackCode = Order::where('corrios_tracking_code', $item)->value('id');
               array_push($orderIds, $trackCode);
            }
        }
        if (!$orderIds) {
            return apiResponse(false,"Please provide order IDs.");
        }

        $orders = $consolidatedDomesticLabelRepository->getInternationalOrders($orderIds);
        if ($orders->isEmpty()) {
            return apiResponse(false,"Selected orders already have domestic label");
        }

        $error = $consolidatedDomesticLabelRepository->getErrors();

        if(!$error){

            $totalWeight = $consolidatedDomesticLabelRepository->getTotalWeight($orders);
            $domesticLabelRepository->handle();

            $order = Order::find($orderIds[0]);
            if(request()->unit == 'kg/cm' && request()->weight > $totalWeight['totalWeightInKg']) {
                $proceed = true;
            }elseif(request()->unit == 'lbs/in' && request()->weight > $totalWeight['totalWeightInLbs']) {
                $proceed = true;
            } else {
                $proceed = false;
            }

            if($proceed = true) {

                $shippingServices = $domesticLabelRepository->getShippingServices($order);
                $label = $domesticLabelRepository->getDomesticLabel($order);
                return apiResponse(true,"Label Successfully Printed",[
                    'weight' => $totalWeight,
                    'label' => $label
                ]);
            }
            
            return apiResponse(false,"Not a Valid Weight",[
                'error' => "Given weight must be greater than the order weight",
            ]);

        }
        return apiResponse(false, $error);
    }
}
