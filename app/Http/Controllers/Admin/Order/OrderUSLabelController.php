<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use App\Models\State;
use App\Http\Controllers\Controller;
use App\Repositories\USLabelRepository;

class OrderUSLabelController extends Controller
{
    public function index(Order $order, USLabelRepository $usLabelRepostory)
    {
        $this->authorize('canPrintLable',$order);

        $usShippingServices = $usLabelRepostory->shippingServices($order);
        $errors = $usLabelRepostory->getErrors();
        
        $states = State::query()->where("country_id", 250)->get(["name","code","id"]);

        return view('admin.orders.us-label.index',compact('order', 'states', 'usShippingServices', 'errors'));
        
    }
}
