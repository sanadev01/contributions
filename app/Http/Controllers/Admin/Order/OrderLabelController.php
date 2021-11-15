<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Facades\CorreosChileFacade;
use App\Http\Controllers\Controller;
use App\Repositories\LabelRepository;
use Illuminate\Support\Facades\Storage;
use App\Repositories\USPSLabelRepository;
use App\Repositories\CorrieosChileLabelRepository;
use App\Repositories\CorrieosBrazilLabelRepository;

class OrderLabelController extends Controller
{
    public function index(Request $request, Order $order)
    {
        $this->authorize('canPrintLable',$order);
        return view('admin.orders.label.index',compact('order'));
    }

    public function store(Request $request, Order $order, LabelRepository $labelRepository)
    {
        $this->authorize('canPrintLable',$order);

        if(!$order->isPaid()){
            $error = 'Error: Payment is Pending';
            $buttonsOnly = $request->has('buttons_only');
            return view('admin.orders.label.label',compact('order','error','buttonsOnly'));
        }
        if ( !$order->is_paid){
            $error = 'Error: Payment is Pending';
            $buttonsOnly = $request->has('buttons_only');
            return view('admin.orders.label.label',compact('order','error','buttonsOnly'));
        }
        // if($order->shippingService->api == ShippingService::API_CORREIOS){
            return $this->handleCorreiosLabels($request,$order);
        // }
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
        $buttonsOnly = $request->has('buttons_only');
        return view('admin.orders.label.label',compact('order','error','buttonsOnly'));
    }

   

    public function handleCorreiosLabels(Request $request, Order $order)
    {
        $error = null;

        $chile_labelRepository = new CorrieosChileLabelRepository();

        $labelRepository = new CorrieosBrazilLabelRepository();

        $usps_labelRepository = new USPSLabelRepository();
        
        if($order->recipient->country_id == Order::CHILE && $request->update_label === 'false')
        {
            $chile_labelRepository->handle($order, $request);

            $error = $chile_labelRepository->getChileErrors();
            return $this->renderLabel($request, $order, $error);
        }

        if($order->recipient->country_id == Order::USPS && $request->update_label === 'false')
        {
            $usps_labelRepository->handle($order);

            $error = $usps_labelRepository->getUSPSErrors();
            return $this->renderLabel($request, $order, $error);
        }
        
        if ( $request->update_label === 'true' ){
            
            if($order->recipient->country_id == Order::CHILE)
            {
                $chile_labelRepository->update($order, $request);

                $error = $chile_labelRepository->getChileErrors();
                return $this->renderLabel($request, $order, $error);
            }
            
            if($order->recipient->country_id == Order::USPS)
            {
                $usps_labelRepository->update($order);

                $error = $usps_labelRepository->getUSPSErrors();
                return $this->renderLabel($request, $order, $error);
            }

            $labelRepository->update($order);
        } else{
            $labelRepository->get($order);
        }

        $order->refresh();

        $error = $labelRepository->getError();
        
        return $this->renderLabel($request, $order, $error);
    }
    
    public function renderLabel($request, $order, $error)
    {

        $buttonsOnly = $request->has('buttons_only');

        return view('admin.orders.label.label',compact('order','error' ,'buttonsOnly'));
    }
}
