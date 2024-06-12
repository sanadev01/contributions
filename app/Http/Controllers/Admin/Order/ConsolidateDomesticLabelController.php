<?php

namespace App\Http\Controllers\Admin\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\USLabelRepository;
use App\Repositories\ConsolidateDomesticLabelRepository;

class ConsolidateDomesticLabelController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, ConsolidateDomesticLabelRepository $domesticLabelRepository, USLabelRepository $usLabelRepostory)
    {
        $validated = $request->validate([
            'command' => 'required|max:255',
            'data' => 'required',
        ]);

        $orderIds = array_map( function($id) { return decrypt($id);
        }, json_decode($validated['data'],true));
        
        if (!$orderIds) {
            session()->flash('alert-danger', 'orders must be selected');
            return back();
        }

        $orders = $domesticLabelRepository->getInternationalOrders($orderIds);
        
        if ($orders->isEmpty()) {
            session()->flash('alert-danger', 'Selected orders already have domestic label');
            return back();
        }

        $totalWeight = $domesticLabelRepository->getTotalWeight($orders);

        $errors = $domesticLabelRepository->getErrors();
        $states = $domesticLabelRepository->getStates();
        
        return view('admin.orders.consolidate-domestic-label', compact('orders', 'states', 'errors', 'totalWeight'));
    }
}
