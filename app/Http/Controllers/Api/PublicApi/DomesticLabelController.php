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
        //dd($request->toArray());
        $validated = $request->validate([
            'tracking_code' => 'required',
            //'sender' => 'required',
            'recipient' => 'required',
            'service' => 'required',
            'total_price' => 'required',
            //'consolidate_dimensions' => 'required',
        ]);

        $trackIds = json_decode(json_encode($validated['tracking_code']), true);
        //dd($trackIds);
        $orderIds = [];
        if (count($trackIds) >= 1) {
            foreach ($trackIds as $key => $item) {
                $trackCode = Order::where('corrios_tracking_code', $item)->value('id');
               array_push($orderIds, $trackCode);
            }
        }
        //dd(json_encode($orderIds));
        if (!$orderIds) {
            return apiResponse(false,"Please provide order IDs.");
        }

        $orders = $consolidatedDomesticLabelRepository->getInternationalOrders($orderIds);
        //dd($orders);
        if ($orders->isEmpty()) {
            return apiResponse(false,"Selected orders already have domestic label");
        }

        $error = $consolidatedDomesticLabelRepository->getErrors();

        if(!$error){

            $totalWeight = $consolidatedDomesticLabelRepository->getTotalWeight($orders);
            $domesticLabelRepository->handle();

            $order = Order::find($orderIds[0]);
            if(request()->service == "UPS") { request()->merge(['service' => 3]); }
            elseif(request()->service == "FedEx") { request()->merge(['service' => 4]); }
            elseif(request()->service == "USPS") { request()->merge(['service' => 3440]); }
            else { request()->merge(['service' => 3441]); }
            //dd($order);
            $shippingServices = $domesticLabelRepository->getShippingServices($order);
            $label = $domesticLabelRepository->getDomesticLabel($order);
            //$rates = $domesticLabelRepository->getRatesForDomesticServices($shippingServices);

            return apiResponse(true,"Total Weight of Orders",[
                'weight' => $totalWeight,
                'label' => $label
            ]);

        }
        return apiResponse(false, $error);
    }
}
