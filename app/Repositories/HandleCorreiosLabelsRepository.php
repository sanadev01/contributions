<?php
namespace App\Repositories;

use App\Models\Order;
use Illuminate\Http\Request;

use App\Repositories\UPSLabelRepository;
use App\Repositories\GePSLabelRepository;
use App\Repositories\USPSLabelRepository;
use App\Repositories\FedExLabelRepository;

/**
 * Use for Sinerlog integration
 */
use App\Repositories\CorrieosChileLabelRepository;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Repositories\SwedenPostLabelRepository;
use App\Models\ShippingService;

class HandleCorreiosLabelsRepository{


    public $order;
    public $request;
    public $error;

    public function __construct(Request $request, Order $order)
    {
        $this->order = $order;
        $this->request = $request;
        $this->error = null;
    }
    public function handle()
    {
        $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();;
        $error = null;
        if($this->order->recipient->country_id == Order::CHILE && $this->request->update_label === 'false')
        {
            $corrieosChileLabelRepository = new CorrieosChileLabelRepository();

            $corrieosChileLabelRepository->handle($this->order, $this->request);
            $error = $corrieosChileLabelRepository->getChileErrors();
            return $this->renderLabel($this->request, $this->order, $error);
        }

        if($this->order->recipient->country_id == Order::US && $this->request->update_label === 'false')
        {
            if($this->order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $this->order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS)
            {
                $uspsLabelRepository = new USPSLabelRepository();
                $uspsLabelRepository->handle($this->order); 
                $error = $uspsLabelRepository->getUSPSErrors();
                return $this->renderLabel($this->request, $this->order, $error);
            }

            if($this->order->shippingService->service_sub_class == ShippingService::FEDEX_GROUND)
            {
                $fedExLabelRepository = new FedExLabelRepository();
                $fedExLabelRepository->handle($this->order);

                $error = $fedExLabelRepository->getFedExErrors();
                return $this->renderLabel($this->request, $this->order, $error);
            }

            if($this->order->shippingService->service_sub_class == ShippingService::UPS_GROUND)
            {
                $upsLabelRepository = new UPSLabelRepository();
                $upsLabelRepository->handle($this->order);
                $error = $upsLabelRepository->getUPSErrors();
                return $this->renderLabel($this->request, $this->order, $error);
            }
            
        }

        if ($this->order->recipient->country_id != Order::US && $this->request->update_label === 'false')
        {
            if($this->order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL || $this->order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL)
            {
                
                $uspsLabelRepository = new USPSLabelRepository();
                $uspsLabelRepository->handle($this->order);
                $error = $uspsLabelRepository->getUSPSErrors();
                return $this->renderLabel($this->request, $this->order, $error);
            }
        }

        if($this->order->shippingService->isGePSService()){

            $gepsLabelRepository = new GePSLabelRepository();
            $gepsLabelRepository->get($this->order);

            $error = $gepsLabelRepository->getError();
            return $this->renderLabel($this->request, $this->order, $error);
        }

        if($this->order->shippingService->isSwedenPostService()){

            $swedenpostLabelRepository = new SwedenpostLabelRepository();
            $swedenpostLabelRepository->get($this->order);
            $error = $swedenpostLabelRepository->getError();
            return $this->renderLabel($this->request, $this->order, $error);
        }
        
        if ( $this->request->update_label === 'true' ){
            
            if($this->order->recipient->country_id == Order::CHILE)
            {
                $corrieosChileLabelRepository = new CorrieosChileLabelRepository();
                $corrieosChileLabelRepository->update($this->order, $this->request);

                $error = $corrieosChileLabelRepository->getChileErrors();
                return $this->renderLabel($this->request, $this->order, $error);
            }
            
            if($this->order->recipient->country_id == Order::US)
            {
                if($this->order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $this->order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS)
                {
                    $uspsLabelRepository = new USPSLabelRepository();
                    $uspsLabelRepository->update($this->order);

                    $error = $uspsLabelRepository->getUSPSErrors();
                    return $this->renderLabel($this->request, $this->order, $error);
                }

                if($this->order->shippingService->service_sub_class == ShippingService::FEDEX_GROUND)
                {
                    $fedExLabelRepository =  new FedExLabelRepository();
                    $fedExLabelRepository->update($this->order);

                    $error = $fedExLabelRepository->getFedExErrors();
                    return $this->renderLabel($this->request, $this->order, $error);
                }
                $upsLabelRepository = new UPSLabelRepository();
                $upsLabelRepository->update($this->order);
                $error = $upsLabelRepository->getUPSErrors();

                return $this->renderLabel($this->request, $this->order, $error);
            }

            $corrieosBrazilLabelRepository->update($this->order);
        } else{
            $corrieosBrazilLabelRepository->get($this->order);
        }

        $this->order->refresh();
        $error = $corrieosBrazilLabelRepository->getError();
        
        return $this->renderLabel($this->request, $this->order, $error);
    }
    
      
    public function renderLabel($request, $order, $error)
    {

        $buttonsOnly = $this->request->has('buttons_only');

        return view('admin.orders.label.label',compact('order','error' ,'buttonsOnly'));
    }

}