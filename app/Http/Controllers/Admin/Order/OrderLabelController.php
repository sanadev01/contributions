<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Facades\CorreosChileFacade;
use App\Services\UPS\UPSLabelMaker;
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

        $shipping_service_code =  $this->getOrderShippingService($order->shipping_service_id);
        
        if($order->recipient->country_id == Order::CHILE && $request->update_label === 'false')
        {
            $chile_labelRepository->handle($order, $request);

            $error = $chile_labelRepository->getChileErrors();
            return $this->renderLabel($request, $order, $error);
        }

        if($order->recipient->country_id == Order::US && $request->update_label === 'false')
        {
            
            if($shipping_service_code == ShippingService::USPS_PRIORITY || $shipping_service_code == ShippingService::USPS_FIRSTCLASS)
            {
                $usps_labelRepository->handle($order);

                $error = $usps_labelRepository->getUSPSErrors();
                return $this->renderLabel($request, $order, $error);
            }
            
            $labelPrinter = new UPSLabelMaker();
            $labelPrinter->setOrder($order);
            $labelPrinter->saveLabel();
            return $this->renderLabel($request, $order, $error);
            
        }
        
        if ( $request->update_label === 'true' ){
            
            if($order->recipient->country_id == Order::CHILE)
            {
                $chile_labelRepository->update($order, $request);

                $error = $chile_labelRepository->getChileErrors();
                return $this->renderLabel($request, $order, $error);
            }
            
            if($order->recipient->country_id == Order::US)
            {
                if($shipping_service_code == (ShippingService::USPS_PRIORITY || ShippingService::USPS_FIRSTCLASS))
                {
                    $usps_labelRepository->update($order);

                    $error = $usps_labelRepository->getUSPSErrors();
                    return $this->renderLabel($request, $order, $error);
                }

                $labelPrinter = new UPSLabelMaker();
                $labelPrinter->setOrder($order);
                $labelPrinter->saveLabel();
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

    private function getOrderShippingService($order_shipping_service_id)
    {
        $shipping_service = ShippingService::find($order_shipping_service_id);

        return $shipping_service->service_sub_class;
    }
}
