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
/**
 * Use for Sinerlog integration
 */
use App\Repositories\SinerlogLabelRepository;

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

        $labelSinerlogRep = new SinerlogLabelRepository(); 

        /**
         * Sinerlog modification
         * Checks if shipping service ia a Sinerlog service
         */
        if(
            $order->recipient->country_id == 30 
            && 
            $order->shippingService()->find($order->shipping_service_id)->api == 'sinerlog'
        ){
            return $this->handleSinerlogLabels($request,$order);
        }
        else {
            return $this->handleCorreiosLabels($request,$order);
        }         

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
    

    /**
     * Sinerlog modification
     * Function to handle Sinerlog label
     */
    public function handleSinerlogLabels(Request $request, Order $order)
    {
        $error = null;
        /**
         * Variable to handle Sinerlog label creation
         */
        $labelSinerlogRep = new SinerlogLabelRepository();       

        if (!$order->hasCN23()){          
            $renderLabel = $labelSinerlogRep->update($order);
        } else{
            $renderLabel = $labelSinerlogRep->get($order);
        }

        $order->refresh();

        $error = $labelSinerlogRep->getError();
        
        return $this->renderSinerlogLabel($request, $order, $error, $renderLabel);
    }

    public function renderLabel($request, $order, $error)
    {

        $buttonsOnly = $request->has('buttons_only');

        return view('admin.orders.label.label',compact('order','error' ,'buttonsOnly'));
    }

    /**
     * Sinerlog modification
     * Function to render Sinerlog label
     */
    public function renderSinerlogLabel($request, $order, $error, $renderLabel)
    {
        $buttonsOnly = $request->has('buttons_only');

        return view('admin.orders.label.label',compact('order','error', 'renderLabel' ,'buttonsOnly'));
    }    
}
