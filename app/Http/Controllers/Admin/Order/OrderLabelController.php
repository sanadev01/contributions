<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\LabelRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderLabelController extends Controller
{
    public function index(Request $request, Order $order)
    {
        return view('admin.orders.label.index',compact('order'));
    }

    public function store(Request $request, Order $order, LabelRepository $labelRepository)
    {

        $labelData = null;
        $error = null;

        if ( $request->update_label === 'true' ){
            $labelData = $labelRepository->update($order);
        }else{
            $labelData = $labelRepository->get($order);
        }

        $order->refresh();

        if ( $labelData ){
            Storage::put("labels/{$order->corrios_tracking_code}.pdf", $labelData);
        }
        
        $error = $labelRepository->getError();

        return view('admin.orders.label.label',compact('order','error'));
    }
}
