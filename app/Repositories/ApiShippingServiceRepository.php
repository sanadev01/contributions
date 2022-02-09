<?php

namespace App\Repositories;

use App\Facades\UPSFacade;
use App\Facades\USPSFacade;
use App\Models\ShippingService;

class ApiShippingServiceRepository
{
    public $error;

    public function isAvalaible($request, $volumeWeight)
    {
        $shippingService = ShippingService::find($request->parcel['service_id']);

        if ($volumeWeight > $shippingService->max_weight_allowed) {
            $this->error = 'The weight of the package exceeds the maximum allowed weight for this service.';
            return false;
        }
        
        if($shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS)
        {
            if(!setting('usps', null, auth()->user()->id))
            {
                $this->error = 'Seleceted Shipping service is not available for your account.';
                return false;
            }
        }

        if($shippingService->service_sub_class == ShippingService::UPS_GROUND)
        {
            if(!setting('ups', null, auth()->user()->id))
            {
                $this->error = 'Seleceted Shipping service is not available for your account.';
                return false;
            }
        }

        return true;
    }

    public function getUSShippingServiceRate($order)
    {
        if ($order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS) 
        {
            $response = USPSFacade::getRecipientRates($order, $order->shippingService->service_sub_class);

            if($response->success == true)
            {
                $order->update([
                    'user_declared_freight' => $response->data['total_amount'],
                ]);

                return true;
            }

            $this->error = 'server error, could not get rates';
        }

        if ($order->shippingService->service_sub_class == ShippingService::UPS_GROUND)
        {
            $response = UPSFacade::getRecipientRates($order, $order->shippingService->service_sub_class);

            if($response->success == true)
            {
                $order->update([
                    'user_declared_freight' => number_format($response->data['RateResponse']['RatedShipment']['TotalCharges']['MonetaryValue'], 2),
                ]);

                return true;
            }

            $this->error = $response->error['response']['errors'][0]['message'];
        }

        return false;
    }

    public function getError()
    {
        return $this->error;
    }
}
