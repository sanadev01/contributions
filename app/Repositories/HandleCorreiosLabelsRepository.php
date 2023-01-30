<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Repositories\UPSLabelRepository;
use App\Repositories\GePSLabelRepository;
use App\Repositories\USPSLabelRepository;
use App\Repositories\FedExLabelRepository;
use App\Repositories\SwedenPostLabelRepository;
use App\Repositories\CorrieosChileLabelRepository;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Repositories\ColombiaLabelRepository;

class HandleCorreiosLabelsRepository
{
    public $order;
    public $request;
    public $error;
    public $update;

    public function __construct(Request $request, Order $order)
    {
        $this->order = $order;
        $this->request = $request;
        $this->error = null;
        $this->update = $this->request->update_label  === 'false'?false:true;
    }
    public function handle()
    {
        if ($this->order->recipient->country_id == Order::BRAZIL) {

            if ($this->order->shippingService->isGePSService()) {

                return $this->gepsLabel();
            }

            if ($this->order->shippingService->isSwedenPostService()) {
                
                return $this->swedenPostLabel();
            }
            if ($this->order->shippingService->isCorreiosService()) {
                return $this->corriesBrazilLabel();
            }
            if ($this->order->shippingService->is_milli_express) {
                return $this->mileExpressLabel();
            }
        }
        if ($this->order->recipient->country_id == Order::CHILE) {

            return $this->corrieosChileLabel();
        }

        if ($this->order->recipient->country_id == Order::COLOMBIA && $this->order->shippingService->isColombiaService()) {

            return $this->colombiaLabel();
        }


        if ($this->order->recipient->country_id == Order::US) {
            if ($this->order->shippingService->is_usps_priority || $this->order->shippingService->is_usps_firstclass) {
                return $this->uspsLabel();
            }

            if ($this->order->shippingService->is_fedex_ground) {
                return $this->fedExLabel();
            }

            if ($this->order->shippingService->is_ups_ground) {
                return $this->upsLabel();
            }
        }

        if ($this->order->recipient->country_id != Order::US) {
            if ($this->order->shippingService->is_usps_priority_international || $this->order->shippingService->is_usps_firstclass_international) {
                return $this->uspsLabel();
            }
        }

        if($this->order->shippingService->isPostNLService()){
            return $this->postNLLabel();
        }
        
        return $this->corriesBrazilLabel();
    }

    public function colombiaLabel()
    {
        $colombiaLabelRepository = new ColombiaLabelRepository($this->order);
        $colombiaLabelRepository->run($this->order,$this->update); 
        return $this->renderLabel($this->request, $this->order, $colombiaLabelRepository->getError());
    }

    public function mileExpressLabel()
    {
        $mileExpressLabelRepository = new MileExpressLabelRepository();
        $mileExpressLabelRepository->run($this->order,$this->update); 
        return $this->renderLabel($this->request, $this->order, $mileExpressLabelRepository->getError());
    }

    public function postNLLabel()
    {
        $postNLLabelRepository = new POSTNLLabelRepository();
        $postNLLabelRepository->run($this->order,$this->update); 
        return $this->renderLabel($this->request, $this->order, $postNLLabelRepository->getError());
    }

    // public function colombiaLabel()
    // {
    //     $colombiaLabelRepository = new ColombiaLabelRepository($this->order);
    //     $colombiaLabelRepository->run($this->order,$this->update); 
    //     return $this->renderLabel($this->request, $this->order, $colombiaLabelRepository->getError());
    // }

    // public function mileExpressLabel()
    // {
    //     $mileExpressLabelRepository = new MileExpressLabelRepository();
    //     $mileExpressLabelRepository->run($this->order,$this->update); 
    //     return $this->renderLabel($this->request, $this->order, $mileExpressLabelRepository->getError());
    // }

    // public function postNLLabel()
    // {
    //     $postNLLabelRepository = new POSTNLLabelRepository();
    //     $postNLLabelRepository->run($this->order,$this->update); 
    //     return $this->renderLabel($this->request, $this->order, $postNLLabelRepository->getError());
    // }

    public function gepsLabel()
    {
        $gepsLabelRepository = new GePSLabelRepository(); ///by default consider false
        $gepsLabelRepository->run($this->order,$this->update);
        return $this->renderLabel($this->request, $this->order, $gepsLabelRepository->getError());
    }

    public function swedenPostLabel()
    {
        $swedenpostLabelRepository = new SwedenpostLabelRepository(); 
        $swedenpostLabelRepository->run($this->order,$this->update); //by default consider false
        return $this->renderLabel($this->request, $this->order, $swedenpostLabelRepository->getError());
    }

    public function corrieosChileLabel()
    { 
        $corrieosChileLabelRepository = new CorrieosChileLabelRepository(); 
        $corrieosChileLabelRepository->run($this->order,$this->update); 
        return $this->renderLabel($this->request, $this->order, $corrieosChileLabelRepository->getChileErrors());
    }

    public function corriesBrazilLabel()
    {
        $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository(); 
        $corrieosBrazilLabelRepository->run($this->order,$this->update); 
        return $this->renderLabel($this->request, $this->order,$corrieosBrazilLabelRepository->getError());
    }

    public function uspsLabel()
    {
        $uspsLabelRepository = new USPSLabelRepository(); 
        $uspsLabelRepository->run($this->order,$this->update); 
        return $this->renderLabel($this->request, $this->order, $uspsLabelRepository->getUSPSErrors());
    }

    public function fedExLabel()
    {
        $fedExLabelRepository =  new FedExLabelRepository(); 
        $fedExLabelRepository->run($this->order,$this->update); 
        return $this->renderLabel($this->request, $this->order, $fedExLabelRepository->getFedExErrors());
    }

    public function upsLabel()
    {
        $upsLabelRepository = new UPSLabelRepository(); 
        $upsLabelRepository->run($this->order,$this->update); 
        return $this->renderLabel($this->request, $this->order, $upsLabelRepository->getUPSErrors());
    }

    public function renderLabel($request, $order, $error)
    {
        $buttonsOnly = $this->request->has('buttons_only');
        return view('admin.orders.label.label', compact('order', 'error', 'buttonsOnly'));
    }
}