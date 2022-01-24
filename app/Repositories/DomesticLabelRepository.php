<?php

namespace App\Repositories;

use App\Facades\USPSFacade;
use App\Models\ShippingService;
use App\Repositories\UPSLabelRepository;
use App\Repositories\USPSLabelRepository;
use App\Repositories\FedExLabelRepository;

class DomesticLabelRepository
{
    protected $upsLabelRepository;
    protected $uspsLabelRepository;
    protected $fedexLabelRepository;

    public $domesticRates = [];
    public $error;

    public function handle()
    {
        $this->upsLabelRepository = new UPSLabelRepository();
        $this->uspsLabelRepository = new USPSLabelRepository();
        $this->fedExLabelRepository = new FedExLabelRepository();
    }

    public function validateAddress($request)
    {
        return $this->callForUSPSAddressApi($request);
    }

    public function getRatesForDomesticServices($request, $usShippingServices)
    {
        $usShippingServices->each(function ($shippingService, $key) use ($request) {
            if ($shippingService['service_sub_class'] == ShippingService::UPS_GROUND) {
                $this->getUPSRates($request, $shippingService['service_sub_class']);
            }

            if ($shippingService['service_sub_class'] == ShippingService::USPS_PRIORITY
                || $shippingService['service_sub_class'] == ShippingService::USPS_FIRSTCLASS) 
            {
                $this->getUSPSRates($request, $shippingService['service_sub_class']);
            }

            if ($shippingService['service_sub_class'] == ShippingService::FEDEX_GROUND) {
               $this->getFedexRates($request, $shippingService['service_sub_class']);
            }
        });

        return $this->domesticRates;
    }

    public function getDomesticLabel($request, $order)
    {
        if ($request->service == ShippingService::UPS_GROUND)
        {
            if($this->upsLabelRepository->getSecondaryLabel($request, $order))
            {
                return true;
            }

            $this->error = $this->upsLabelRepository->getUPSErrors();
            return false;
        }

        if ($request->service == ShippingService::FEDEX_GROUND) 
        {
            // return $this->fedexLabelRepository->getFedexGroundLabel($request, $order);
        }

        if ($request->service == ShippingService::USPS_PRIORITY || $request->service == ShippingService::USPS_FIRSTCLASS) 
        {
            if($this->uspsLabelRepository->getSecondaryLabel($request, $order))
            {
                return true;
            }
            
            $this->error = $this->uspsLabelRepository->getUSPSErrors();
            return false;
        }
    }

    public function getError()
    {
        return $this->error;
    }

    private function getUPSRates($request, $service)
    {
        $request->merge(['service' => $service]);

        $upsRateResponse = $this->upsLabelRepository->getRatesForSender($request);
        if ($upsRateResponse['success'] == true) {
            return array_push($this->domesticRates, ['service' => 'UPS Ground', 'service_code' => $service, 'cost' => $upsRateResponse['total_amount']]);
        }
    }

    private function getUSPSRates($request, $service)
    {
        $request->merge(['service' => $service]);

        $uspsRateResponse = $this->uspsLabelRepository->getRatesForSender($request);
        if ($uspsRateResponse['success'] == true) {
            return array_push($this->domesticRates, ['service' => ($service == ShippingService::USPS_PRIORITY) ? 'USPS Priority' : 'USPS FirstClass', 'service_code' => $service, 'cost' => $uspsRateResponse['total_amount']]);
        }
    }

    private function getFedexRates($request, $service)
    {
        $request->merge(['service' => $service]);

        $fedExRateResponse = $this->fedExLabelRepository->getRatesForSender($request);
        if ($fedExRateResponse['success'] == true) {
            return array_push($this->domesticRates, ['service' => 'FedEx Ground', 'service_code' => $service, 'cost' => $fedExRateResponse['total_amount']]);
        }
    }
    
    private function callForUSPSAddressApi($request)
    {
        return USPSFacade::validateAddress($request);
    }
}
