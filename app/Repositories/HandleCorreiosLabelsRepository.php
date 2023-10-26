<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\UPSLabelRepository;
use App\Repositories\GePSLabelRepository;
use App\Repositories\USPSLabelRepository;
use App\Repositories\FedExLabelRepository;
use App\Repositories\PostPlusLabelRepository;
use App\Repositories\SwedenPostLabelRepository;
use App\Repositories\CorrieosChileLabelRepository;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Repositories\AnjunLabelRepository;
use App\Services\TotalExpress\TotalExpressLabelRepository;
use App\Models\ShippingService;

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
        if ($this->order->shippingService->isSwedenPostService()) {
                return $this->swedenPostLabel();
        }
        if ($this->order->recipient->country_id == Order::BRAZIL) {
            
            if ($this->order->shippingService->isGePSService()) {

                return $this->gepsLabel();
            }
            if ($this->order->shippingService->isCorreiosService() || $this->order->shippingService->is_anjun_china_service_sub_class || $this->order->shippingService->isAnjunService()) {
                return $this->correiosOrAnjun($this->order);
            }
            if ($this->order->shippingService->isPostPlusService()) {
                return $this->postPlusLabel();
            }
            if ($this->order->shippingService->isGSSService()) {
                return $this->uspsGSSLabel();
            }
            if ($this->order->shippingService->is_total_express) {
                return $this->totalExpressLabel();
            }
            
            // if ($this->order->shippingService->is_milli_express) {
            //     return $this->mileExpressLabel();
            // }
        }
        if ($this->order->shippingService->isHDExpressService()) {
            return $this->hdExpressLabel();
        }
        if ($this->order->recipient->country_id == Order::CHILE) {

            return $this->corrieosChileLabel();
        }

        // if ($this->order->recipient->country_id == Order::COLOMBIA && $this->order->shippingService->isColombiaService()) {
        //     return $this->colombiaLabel();
        // }


        if ($this->order->recipient->country_id == Order::US) {
            if ($this->order->shippingService->is_usps_priority || $this->order->shippingService->is_usps_firstclass || $this->order->shippingService->is_usps_ground || $this->order->shippingService->is_gde_priority || $this->order->shippingService->is_gde_first_class) {
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

        // if($this->order->shippingService->isPostNLService()){
        //     return $this->postNLLabel();
        // }
        return $this->correiosOrAnjun($this->order);

    }

    // public function colombiaLabel()
    // {
    //     $colombiaLabelRepository = new ColombiaLabelRepository($this->order);
    //     $colombiaLabelRepository->run($this->order,$this->update); 
    //     return $this->renderLabel($this->request, $this->order, $colombiaLabelRepository->getError());
    // }

    public function hdExpressLabel()
    {
        $hdExpressLabelRepository = new HDExpressLabelRepository();
        $hdExpressLabelRepository->run($this->order,$this->update); 
        return $this->renderLabel($this->request, $this->order, $hdExpressLabelRepository->getError());
    }

    // public function postNLLabel()
    // {
    //     $postNLLabelRepository = new POSTNLLabelRepository();
    //     $postNLLabelRepository->run($this->order,$this->update); 
    //     return $this->renderLabel($this->request, $this->order, $postNLLabelRepository->getError());
    // }

    public function totalExpressLabel()
    {
        $totalExpress = new TotalExpressLabelRepository(); ///by default consider false
        $totalExpress->run($this->order,$this->update);
        return $this->renderLabel($this->request, $this->order, $totalExpress->getError());
    }
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

    public function correiosOrAnjun($order)
    {
        
        $order = $this->updateShippingServiceFromSetting($order);
        if($order->shippingService->is_anjun_china_service_sub_class){
            return $this->anjunChinaLabel();
        }
        return $this->corriesBrazilLabel();
    }

    public function corriesBrazilLabel()
    {
        $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository(); 
        $corrieosBrazilLabelRepository->run($this->order,$this->update); 
        return $this->renderLabel($this->request, $this->order,$corrieosBrazilLabelRepository->getError());
    }
    
    public function anjunChinaLabel()
    {
        return (new AnjunLabelRepository($this->request,$this->order))->run(); 
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

    public function postPlusLabel()
    {
        $postPlusLabelRepository = new PostPlusLabelRepository(); 
        $postPlusLabelRepository->run($this->order,$this->update); //by default consider false
        return $this->renderLabel($this->request, $this->order, $postPlusLabelRepository->getError());
    }

    public function uspsGSSLabel()
    {
        $gssLabelRepository = new GSSLabelRepository(); 
        $gssLabelRepository->run($this->order,$this->update); //by default consider false
        return $this->renderLabel($this->request, $this->order, $gssLabelRepository->getError());
    }

    public function renderLabel($request, $order, $error)
    {
        $buttonsOnly = $this->request->has('buttons_only');
        return view('admin.orders.label.label', compact('order', 'error', 'buttonsOnly'));
    }
    public function updateShippingServiceFromSetting($order) {
        $service_sub_class = $order->shippingService->service_sub_class;
        if($order->corrios_tracking_code){
            return $order;
        }
        $standard = in_array($service_sub_class,[ShippingService::Packet_Standard,ShippingService::AJ_Packet_Standard,ShippingService::AJ_Standard_CN]);
        
        if(setting('china_anjun_api', null, User::ROLE_ADMIN) ){
            if($standard){
                $service_sub_class = ShippingService::AJ_Standard_CN;
            }
            else{
                $service_sub_class = ShippingService::AJ_Express_CN;
            }
        }
        else if(setting('correios_api', null, User::ROLE_ADMIN) ){    
            if($standard){
                $service_sub_class = ShippingService::Packet_Standard;
            }
            else{
                $service_sub_class = ShippingService::Packet_Express;
            }
        }
        // else if(setting('bcn_api', null, User::ROLE_ADMIN) ){
        //     if($standard){
        //         $service_sub_class = ShippingService::BCN_Packet_Standard;
        //     }
        //     else{
        //         $service_sub_class = ShippingService::BCN_Packet_Express;
        //     }
        // }
        else if(setting('anjun_api', null, User::ROLE_ADMIN) ){
            if($standard){
                $service_sub_class = ShippingService::AJ_Packet_Standard;
            }
            else{
                $service_sub_class = ShippingService::AJ_Packet_Express;
            }
        }
        $order->update([
            'shipping_service_id' => (ShippingService::where('service_sub_class',$service_sub_class)->first())->id,
        ]);
        return $order->fresh();
    }
}