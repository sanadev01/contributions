<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Repositories\LabelRepository;
use Illuminate\Support\Facades\Storage;
use App\Repositories\UPSLabelRepository;
use App\Repositories\USPSLabelRepository;
use App\Repositories\CorrieosChileLabelRepository;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Repositories\FedExLabelRepository;

/**
 * Use for Sinerlog integration
 */
use App\Repositories\SinerlogLabelRepository;
use Illuminate\Support\Facades\Log;

class OrderLabelController extends Controller
{
    protected $corrieosChileLabelRepository;
    protected $corrieosBrazilLabelRepository;
    protected $uspsLabelRepository;
    protected $upsLabelRepository;
    protected $fedExLabelRepository;

    public function __construct(CorrieosChileLabelRepository $corrieosChileLabelRepository, CorrieosBrazilLabelRepository $corrieosBrazilLabelRepository, USPSLabelRepository $uspsLabelRepository, UPSLabelRepository $upsLabelRepository, FedExLabelRepository $fedExLabelRepository)
    {
        $this->corrieosChileLabelRepository = $corrieosChileLabelRepository;
        $this->corrieosBrazilLabelRepository = $corrieosBrazilLabelRepository;
        $this->uspsLabelRepository = $uspsLabelRepository;
        $this->upsLabelRepository = $upsLabelRepository;
        $this->fedExLabelRepository = $fedExLabelRepository;
    }
    
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
        // if($order->shippingService->api == ShippingService::API_CORREIOS){
            // return $this->handleCorreiosLabels($request,$order);
        // }
        $labelSinerlogRep = new SinerlogLabelRepository(); 

        /**
         * Sinerlog modification
         * Checks if shipping service ia a Sinerlog service
         */
        if(
            $order->recipient->country_id == Order::BRAZIL 
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
        
        if($order->recipient->country_id == Order::CHILE && $request->update_label === 'false')
        {
            $this->corrieosChileLabelRepository->handle($order, $request);

            $error = $this->corrieosChileLabelRepository->getChileErrors();
            return $this->renderLabel($request, $order, $error);
        }

        if($order->recipient->country_id == Order::US && $request->update_label === 'false')
        {
            if($order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS)
            {
                $this->uspsLabelRepository->handle($order);

                $error = $this->uspsLabelRepository->getUSPSErrors();
                return $this->renderLabel($request, $order, $error);
            }

            if($order->shippingService->service_sub_class == ShippingService::FEDEX_GROUND)
            {
                $this->fedExLabelRepository->handle($order);

                $error = $this->fedExLabelRepository->getFedExErrors();
                return $this->renderLabel($request, $order, $error);
            }

            $this->upsLabelRepository->handle($order);
            $error = $this->upsLabelRepository->getUPSErrors();
            return $this->renderLabel($request, $order, $error);
            
        }
        
        if ( $request->update_label === 'true' ){
            
            if($order->recipient->country_id == Order::CHILE)
            {
                $this->corrieosChileLabelRepository->update($order, $request);

                $error = $this->corrieosChileLabelRepository->getChileErrors();
                return $this->renderLabel($request, $order, $error);
            }
            
            if($order->recipient->country_id == Order::US)
            {
                if($order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS)
                {
                    $this->uspsLabelRepository->update($order);

                    $error = $this->uspsLabelRepository->getUSPSErrors();
                    return $this->renderLabel($request, $order, $error);
                }

                if($order->shippingService->service_sub_class == ShippingService::FEDEX_GROUND)
                {
                    $this->fedExLabelRepository->update($order);

                    $error = $this->fedExLabelRepository->getFedExErrors();
                    return $this->renderLabel($request, $order, $error);
                }

                $this->upsLabelRepository->update($order);
                $error = $this->upsLabelRepository->getUPSErrors();

                return $this->renderLabel($request, $order, $error);
            }

            $this->corrieosBrazilLabelRepository->update($order);
        } else{
            $this->corrieosBrazilLabelRepository->get($order);
        }

        $order->refresh();

        $error = $this->corrieosBrazilLabelRepository->getError();
        
        return $this->renderLabel($request, $order, $error);
    }
    

    /**
     * Sinerlog modification
     * Function to handle Sinerlog label
     */
    public function handleSinerlogLabels(Request $request, Order $order)
    {
        Log::info('Sinerlog label');
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
