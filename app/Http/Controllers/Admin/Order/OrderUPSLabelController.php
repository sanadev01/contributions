<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShippingService;
use App\Repositories\UPSLabelRepository;

class OrderUPSLabelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order)
    {

        $this->authorize('canPrintLable', $order);

        $ups_labelRepository = new UPSLabelRepository();
        $shippingServices = $ups_labelRepository->getShippingServices($order);
        $error = $ups_labelRepository->getUPSErrors();

        if ($error != null) {
            session()->flash('alert-danger', $error);
        }

        $states = State::query()->where("country_id", 250)->get(["name", "code", "id"]);

        return view('admin.orders.ups-label.index', compact('order', 'states', 'shippingServices', 'error'));
    }

    public function ups_sender_rates(Request $request)
    {
        $ups_labelRepository = new UPSLabelRepository();
        return $ups_labelRepository->getRates($request);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Order $order)
    {
        /**
         * Note...
         * has_second_label is the second label against the order
         */
        $this->authorize('canPrintLable', $order);

        $ups_labelRepository = new UPSLabelRepository();

        if (!$order->has_second_label) {
            $ups_labelRepository->buyLabel($request, $order);
        }

        $error = $ups_labelRepository->getUPSErrors();
        if ($error != null) {
            session()->flash('alert-danger', $error);
            return \back()->withInput();
        }

        $order->refresh();

        return redirect()->route('admin.orders.ups-label.index', $order->id);
    }

    public function cancelUPSPickup($id)
    {
        $order = Order::findorfail($id);

        if ($order->us_api_service != ShippingService::UPS_GROUND) {
            session()->flash('alert-danger', 'FedEx Pickup cannot be canceled');
            return \back()->withInput();
        }

        $this->authorize('canPrintLable', $order);

        $ups_labelRepository = new UPSLabelRepository();

        $ups_labelRepository->cancelUPSPickup($order);

        $error = $ups_labelRepository->getUPSErrors();

        if ($error != null) {
            session()->flash('alert-danger', $error);
            return \back()->withInput();
        }

        session()->flash('alert-success', 'Pickup Cancelled Successfully');
        return \back();
    }
}
