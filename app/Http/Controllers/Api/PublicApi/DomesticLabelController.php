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
            'orders' => 'required',
            'sender' => 'required',
            'receiver' => 'required',
        ]);

        //$orderIds = json_decode(json_encode($validated['orders']), true);
        $orderIds = json_decode($validated['orders'], true);
        //dd($orderIds);
        if (!$orderIds) {
            return apiResponse(false,"Please provide order IDs.");
        }

        //$orders = $consolidatedDomesticLabelRepository->getInternationalOrders($orderIds);
        //dd($orders);
        // if ($orders->isEmpty()) {
        //     return apiResponse(false,"Selected orders already have domestic label");
        // }

        $error = $consolidatedDomesticLabelRepository->getErrors();

        if(!$error){
            $order = Order::find($orderIds);
            //$totalWeight = $consolidatedDomesticLabelRepository->getTotalWeight($orders);
            $rates = $domesticLabelRepository->getShippingServices($order);

            return apiResponse(true,"Total Weight of Orders",[
                //'weight' => $totalWeight,
                'rates' => $rates
            ]);

        }
        return apiResponse(false, $error);
    }
}
