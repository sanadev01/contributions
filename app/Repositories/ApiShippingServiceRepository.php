<?php

namespace App\Repositories;

use App\Facades\UPSFacade;
use App\Facades\USPSFacade;
use App\Facades\FedExFacade;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Repositories\OrderRepository;
use App\Http\Controllers\Admin\Order\OrderItemsController;

class ApiShippingServiceRepository
{
    public $error;

    public function isAvalaible($shippingService, $volumeWeight)
    {
        if ($volumeWeight > $shippingService->max_weight_allowed) {
            $this->error = 'The weight of the package exceeds the maximum allowed weight for this service.';
            return false;
        }
        
        if($shippingService->service_sub_class == ShippingService::USPS_PRIORITY || 
            $shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS || 
            $shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL ||
            $shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL ||
            $shippingService->service_sub_class == ShippingService::USPS_GROUND)
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

        if($shippingService->service_sub_class == ShippingService::FEDEX_GROUND)
        {
            if(!setting('fedex', null, auth()->user()->id))
            {
                $this->error = 'Seleceted Shipping service is not available for your account.';
                return false;
            }
        }

        return true;
    }

    public function isAvailableForInternational($shippingService, $volumeWeight)
    {

        if ($volumeWeight > $shippingService->max_weight_allowed) {
            $this->error = 'The weight of the package exceeds the maximum allowed weight for this service.';
            return false;
        }
        
        if(($shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL || $shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) && $volumeWeight <= $shippingService->max_weight_allowed)
        {
            if(!setting('usps', null, auth()->user()->id))
            {
                $this->error = 'Seleceted Shipping service is not available for your account.';
                return false;
            }

            return true;
        }

        return false;
    }

    public function getUSShippingServiceRate($order)
    {
        if ($order->weight > $order->shippingService->max_weight_allowed) {
            
            $this->error = 'The weight of the package exceeds the maximum allowed weight for this service.';
            return false;
        }

        if ($order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS || $order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL ||  $order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL || $order->shippingService->service_sub_class == ShippingService::USPS_GROUND) 
        {
            if(!setting('usps', null, $order->user->id))
            {
                $this->error = 'Seleceted Shipping service is not available for your account.';
                return false;
            }
            $response = USPSFacade::getRecipientRates($order, $order->shippingService->service_sub_class);

            if($response->success == true)
            {
                $order->update([
                    'user_declared_freight' => $response->data['total_amount'],
                ]);

                return true;
            }

            $this->error = $response->message;
        }

        if ($order->shippingService->service_sub_class == ShippingService::UPS_GROUND)
        {
            if(!setting('ups', null, $order->user->id))
            {
                $this->error = 'Seleceted Shipping service is not available for your account.';
                return false;
            }

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

        if ($order->shippingService->service_sub_class == ShippingService::FEDEX_GROUND)
        {
            if(!setting('fedex', null, $order->user->id))
            {
                $this->error = 'Seleceted Shipping service is not available for your account.';
                return false;
            }

            $response = FedExFacade::getRecipientRates($order, $order->shippingService->service_sub_class);

            if($response->success == true)
            {
                $order->update([
                    'user_declared_freight' => number_format($response->data['output']['rateReplyDetails'][0]['ratedShipmentDetails'][0]['totalNetFedExCharge'], 2),
                ]);

                return true;
            }

            $this->error = $response->error['response']['errors'][0]['message'] ?? 'server error, could not get rates';
        }

        return false;
    }

    public function getGSSRates($order) {

        if ($order->weight > $order->shippingService->max_weight_allowed) {
            
            $this->error = 'The weight of the package exceeds the maximum allowed weight for this service.';
            return false;
        }

        if (in_array($order->shippingService->service_sub_class, [ShippingService::GSS_PMI, ShippingService::GSS_EPMEI, ShippingService::GSS_EPMI, ShippingService::GSS_FCM, ShippingService::GSS_EMS]))
        {
            if(!setting('gss', null, $order->user->id))
            {
                $this->error = 'Seleceted Shipping service is not available for your account.';
                return false;
            }
            
            $request = new Request();
            $request->merge(['order_id' => $order->id, 'service' => $order->shippingService->service_sub_class]);
            $orderItemController = new OrderItemsController(new OrderRepository());
            $response = $orderItemController->GSSRates($request);

            if($response['success'])
            {
                $order->update([
                    'user_declared_freight' => $response['total_amount'],
                ]);

                return true;
            }

            $this->error = 'server error, could not get rates';
        }
    }

    public function getError()
    {
        return $this->error;
    }
}
