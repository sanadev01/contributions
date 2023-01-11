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

class HandleCorreiosLabelsRepository
{


    public $order;
    public $request;
    public $error;

    public function __construct(Request $request, Order $order)
    {
        $this->order = $order;
        $this->request = $request;
        $this->error = null;
    }
    //getLabel or updateLabel
    public function handle()
    {
        if ($this->request->update_label === 'false')
            return $this->getLabel();
        else
            return $this->updateLabel();
    }

    //run if $this->request->update_labe == 'true'
    public function updateLabel()
    {
        if ($this->order->shippingService->isGePSService()) {
            return $this->gepsLabel(false);
        }

        if ($this->order->shippingService->isSwedenPostService()) {
            return $this->swedenpostLabel(false);
        }


        if ($this->order->recipient->country_id == Order::CHILE) {
            return $this->corrieosChileLabel(true);
        }

        if ($this->order->recipient->country_id == Order::US) {
            if ($this->order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $this->order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS) {
                return $this->uspsLabel(true);
            }
            if ($this->order->shippingService->service_sub_class == ShippingService::FEDEX_GROUND) {
                return $this->fedExLabel(true);
            }
            return $this->upsLabel(true);
        }
        return $this->corriesBrazilLabel(true);
    }

    //run if $this->request->update_labe == 'false'
    public function getLabel()
    {
        if ($this->order->recipient->country_id == Order::CHILE) {
            return $this->corrieosChileLabel(false);
        }

        if ($this->order->recipient->country_id == Order::US) {
            if ($this->order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $this->order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS) {
                return $this->uspsLabel(false);
            }

            if ($this->order->shippingService->service_sub_class == ShippingService::FEDEX_GROUND) {
                return $this->fedExLabel(false);
            }

            if ($this->order->shippingService->service_sub_class == ShippingService::UPS_GROUND) {
                return $this->upsLabel(false);
            }
        }

        if ($this->order->recipient->country_id != Order::US) {
            if ($this->order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL || $this->order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) {
                return $this->uspsLabel(false);
            }
        }
        if ($this->order->shippingService->isGePSService()) {

            return $this->gepsLabel(false);
        }

        if ($this->order->shippingService->isSwedenPostService()) {
            return $this->swedenpostLabel(false);
        }
        return $this->corriesBrazilLabel(false);
    }


    public function gepsLabel($update)
    {
        $gepsLabelRepository = new GePSLabelRepository();
        if ($update)
            $gepsLabelRepository->update($this->order);
        else
            $gepsLabelRepository->get($this->order);

        $error = $gepsLabelRepository->getError();
        return $this->renderLabel($this->request, $this->order, $error);
    }

    public function swedenpostLabel($update)
    {

        $swedenpostLabelRepository = new SwedenpostLabelRepository();
        if ($update)
            $swedenpostLabelRepository->update($this->order);
        else
            $swedenpostLabelRepository->get($this->order);
        $error = $swedenpostLabelRepository->getError();
        return $this->renderLabel($this->request, $this->order, $error);
    }

    public function corrieosChileLabel($update)
    {

        $corrieosChileLabelRepository = new CorrieosChileLabelRepository();
        if ($update)
            $corrieosChileLabelRepository->update($this->order, $this->request);
        else
            $corrieosChileLabelRepository->handle($this->order, $this->request);
        $error = $corrieosChileLabelRepository->getChileErrors();
        return $this->renderLabel($this->request, $this->order, $error);
    }

    public function corriesBrazilLabel($update)
    {
        $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();

        if ($update)
            $corrieosBrazilLabelRepository->update($this->order);
        else
            $corrieosBrazilLabelRepository->get($this->order);
        $this->order->refresh();
        $error = $corrieosBrazilLabelRepository->getError();
        return $this->renderLabel($this->request, $this->order, $error);
    }

    public function uspsLabel($update)
    {
        $uspsLabelRepository = new USPSLabelRepository();
        if ($update)
            $uspsLabelRepository->update($this->order);
        else
            $uspsLabelRepository->handle($this->order);
        $error = $uspsLabelRepository->getUSPSErrors();
        return $this->renderLabel($this->request, $this->order, $error);
    }

    public function fedExLabel($update)
    {
        $fedExLabelRepository =  new FedExLabelRepository();
        if ($update)
            $fedExLabelRepository->update($this->order);
        else
            $fedExLabelRepository->handle($this->order);

        $error = $fedExLabelRepository->getFedExErrors();
        return $this->renderLabel($this->request, $this->order, $error);
    }

    public function upsLabel($update)
    {

        $upsLabelRepository = new UPSLabelRepository();
        if ($update)
            $upsLabelRepository->update($this->order);
        else
            $upsLabelRepository->handle($this->order);
        $error = $upsLabelRepository->getUPSErrors();

        return $this->renderLabel($this->request, $this->order, $error);
    }


    //this functio render label for service  contain in this class.
    public function renderLabel($request, $order, $error)
    {

        $buttonsOnly = $this->request->has('buttons_only');

        return view('admin.orders.label.label', compact('order', 'error', 'buttonsOnly'));
    }
}
