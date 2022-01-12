<?php

namespace App\Repositories;

use App\Facades\FedExFacade;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\User;
use App\Services\FedEx\FedExShippingService;

class FedExLabelRepository
{
    public $fedExError;
    protected $user_api_profit;
    protected $total_amount_with_profit;

    public function getShippingServices($order)
    {
        $shippingServices = collect();

        $fedexShippingService = new FedExShippingService($order);

        foreach (ShippingService::query()->active()->get() as $shippingService) {
            if ($fedexShippingService->isAvailableFor($shippingService, $order->getWeight('kg'))){
                $shippingServices->push($shippingService);
            }
        }
        
        if($shippingServices->isEmpty())
        {
            $this->fedExError = 'No shipping service is available for this order';
        }

        if($shippingServices->isNotEmpty() && !setting('fedex', null, $order->user->id))
        {
            $this->fedExError = 'FedEx is not enabled for your account';
            $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                return $shippingService->service_sub_class != ShippingService::FEDEX_GROUND;
            });
        }

        return $shippingServices;
    }

    public function getSecondaryLabel($request, $order)
    {
        if ( $request->total_price > getBalance())
        {
            $this->ups_errors = 'Not Enough Balance. Please Recharge your account.';
           
            return false;
        }
        
        $request->merge(['sender_phone' => $order->user->phone]);
        $response = FedExFacade::createShipmentForSender($order, $request);
    }

    public function getRates($request)
    {
        $order = Order::find($request->order_id);
        $response = FedExFacade::getSenderRates($order, $request);

        if($response->success == true)
        {
            $fedExRate = $response->data['output']['rateReplyDetails'][0]['ratedShipmentDetails'][0]['totalNetFedExCharge'];
            $this->addProfit($order->user, $fedExRate);
            
            return (Array)[
                'success' => true,
                'total_amount' => round($this->total_amount_with_profit, 2),
            ];
        }
        return (Array)[
            'success' => false,
            'message' => 'server error, could not get rates',
        ]; 
    }

    private function addProfit($user, $fedExRate)
    {
        $this->user_api_profit = setting('fedex_profit', null, $user->id);

        if($this->user_api_profit == null || $this->user_api_profit == 0)
        {
            $this->user_api_profit = setting('fedex_profit', null, User::ROLE_ADMIN);
        }

        $profit = $fedExRate * ($this->user_api_profit / 100);

        $this->total_amount_with_profit = $fedExRate + $profit;

        return true;
    }

    public function getFedExErrors()
    {
        return $this->fedExError;
    }
}
