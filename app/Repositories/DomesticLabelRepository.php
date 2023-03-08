<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use App\Models\Recipient;
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

    public $upsShippingServices;
    public $uspsShippingServices;
    public $fedExShippingServices;

    public $upsErrors;
    public $uspsErrors;
    public $fedExErrors;

    public $domesticRates = [];
    public $error;

    public function getTempOrder($request)
    {
        $order = new Order();
        $order->id = 1;
        $order->user = $request->user;
        $order->weight = $request->weight;
        $order->length = $request->length;
        $order->width = $request->width;
        $order->height = $request->height;
        $order->measurement_unit = $request->unit;
        $order->recipient = $this->createRecipient();
        $order->refresh();

        $order->weight = $order->getWeight('kg');
        return $order;
    }

    public function getShippingServices($order)
    {
        return $this->getDomesticShippingServices($order);
    }

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

    public function getRatesForDomesticServices($usShippingServices)
    {
        $usShippingServices->each(function ($shippingService, $key){
            if ($shippingService['service_sub_class'] == ShippingService::UPS_GROUND) {
                $this->getUPSRates( $shippingService['service_sub_class']);
            }

            if ($shippingService['service_sub_class'] == ShippingService::USPS_PRIORITY
                || $shippingService['service_sub_class'] == ShippingService::USPS_FIRSTCLASS 
                || $shippingService['service_sub_class'] == ShippingService::USPS_GROUND) 
            {
                $this->getUSPSRates( $shippingService['service_sub_class']);
            }

            if ($shippingService['service_sub_class'] == ShippingService::FEDEX_GROUND) {
               $this->getFedexRates($shippingService['service_sub_class']);
            }
        });

        return $this->domesticRates;
    }

    public function getDomesticLabel($order)
    {
        if (request()->service == ShippingService::UPS_GROUND)
        {
            if($this->upsLabelRepository->getSecondaryLabel($order))
            {
                return true;
            }

            $this->error = $this->upsLabelRepository->getUPSErrors();
            return false;
        }

        if (request()->service == ShippingService::FEDEX_GROUND) 
        {
            if($this->fedExLabelRepository->getSecondaryLabel($order))
            {
                return true;
            }

            $this->error = $this->fedExLabelRepository->getFedExErrors();
            return false;
        }

        if (request()->service == ShippingService::USPS_PRIORITY || request()->service == ShippingService::USPS_FIRSTCLASS || request()->service == ShippingService::USPS_GROUND) 
        {
            if($this->uspsLabelRepository->getSecondaryLabel($order))
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

    private function getUPSRates($service)
    {
        request()->merge(['service' => $service]);
        
        $upsRateResponse = $this->upsLabelRepository->getRatesForSender();
        if ($upsRateResponse['success'] == true) {
            return array_push($this->domesticRates, ['service' => 'UPS Ground', 'service_code' => $service, 'cost' => $upsRateResponse['total_amount']]);
        }
    }

    private function getUSPSRates($service)
    {
        request()->merge(['service' => $service]);

        $uspsRateResponse = $this->uspsLabelRepository->getRatesForSender();
        if ($uspsRateResponse['success'] == true) {
            return array_push($this->domesticRates, ['service' => ($service == ShippingService::USPS_PRIORITY) ?  'USPS Priority' : (($service == ShippingService::USPS_GROUND)? 'USPS Ground' : 'USPS FirstClass'), 'service_code' => $service, 'cost' => $uspsRateResponse['total_amount']]);
        }
    }

    private function getFedexRates($service)
    {
        request()->merge(['service' => $service]);

        $fedExRateResponse = $this->fedExLabelRepository->getRatesForSender(request());
        if ($fedExRateResponse['success'] == true) {
            return array_push($this->domesticRates, ['service' => 'FedEx Ground', 'service_code' => $service, 'cost' => $fedExRateResponse['total_amount']]);
        }

        $this->error = $fedExRateResponse['message'];
    }
    
    private function callForUSPSAddressApi($request)
    {
        return USPSFacade::validateAddress($request);
    }

    private function createRecipient()
    {
        $recipient = new Recipient();
        $recipient->first_name = 'Marcio';
        $recipient->last_name = 'Fertias';
        $recipient->phone = '+13058885191';
        $recipient->email = 'homedelivery@homedeliverybr.com';
        $recipient->country_id = Country::US;
        $recipient->state_id = State::FL;
        $recipient->address = '8305 NW 116TH AVENUE';
        $recipient->city = 'Miami';
        $recipient->zipcode = '33178';
        $recipient->account_type = 'individual';

        return $recipient;
    }

    private function getDomesticShippingServices($order)
    {
        $shippingServices = collect();

        $this->upsShippingServices =  $this->upsLabelRepository->getShippingServices($order);
        $this->upsErrors = $this->upsLabelRepository->getUPSErrors();

        $this->uspsShippingServices =  $this->uspsLabelRepository->getShippingServices($order);
        $this->uspsErrors = $this->uspsLabelRepository->getUSPSErrors();

        $this->fedExShippingServices =  $this->fedExLabelRepository->getShippingServices($order);
        $this->fedExErrors = $this->fedExLabelRepository->getFedExErrors();

        if ($this->upsShippingServices->isNotEmpty()) 
        {
            $shippingServices = $shippingServices->merge($this->upsShippingServices);
        }

        if ($this->uspsShippingServices->isNotEmpty()) 
        {
            $shippingServices = $shippingServices->merge($this->uspsShippingServices);
        }

        if ($this->fedExShippingServices->isNotEmpty()) 
        {
            $shippingServices = $shippingServices->merge($this->fedExShippingServices);
        }

        return $shippingServices;
    }

    public function getShippingServiceErrors()
    {
        $errors = [];

        if ($this->upsErrors) 
        {
            array_push($errors, $this->upsErrors);
        }

        if ($this->uspsErrors) 
        {
            array_push($errors, $this->uspsErrors);
        }

        if ($this->fedExErrors) 
        {
            array_push($errors, $this->fedExErrors);
        }

        return $errors;
    }
}
