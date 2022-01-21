<?php


namespace App\Repositories;


use App\Models\User;
use App\Models\Order;
use App\Facades\UPSFacade;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use App\Services\UPS\UPSLabelMaker;
use Illuminate\Support\Facades\Log;
use App\Services\UPS\UPSShippingService;


class UPSLabelRepository
{
    protected $shipping_service_code;
    public $upsError;
    protected $user_api_profit;
    protected $total_amount_with_profit;
    protected $totalUpsCost = 0;
    protected $pickupResponse;

    public $order;

    public function handle($order)
    {
        if($order->api_response == null)
        {
            $this->getPrimaryLabelForRecipient($order);

        }

        return true;
    }

    public function update($order)
    {
        $this->getPrimaryLabelForRecipient($order);
    }

    public function getSecondaryLabel($request, $order)
    {

        if($this->checkUserBalance($request->total_price) && $this->getSecondaryLabelForSender($request, $order))
        {
            return true;
        }

        return false;
    }

    public function cancelUPSPickup($order)
    {
        $response = UPSFacade::cancelPickup($order->apiPickupResponse()->PickupCreationResponse->PRN);
        
        if($response->success == true)
        {
            $order->update([
                'api_pickup_response' => null,
            ]);

            return true;
        }

        $this->upsError = $response->error['response']['errors'][0]['message'];
        return false;
    }

    private function getPrimaryLabelForRecipient($order)
    {
        $response = UPSFacade::generateLabel($order);
        
        if($response->success == true)
        {
            //storing response in orders table
            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data['ShipmentResponse']['ShipmentResults']['ShipmentIdentificationNumber'],
            ]);

            $order->refresh();
            // store order status in order tracking
            $this->addOrderTracking($order);

            // Connert PNG label To PDF
            $this->convertLabelToPDF($order);

            return true;

        } else {

            $this->upsError = $response->error['response']['errors'][0]['message'];
            return null;
        }
        
    }

    public function getSecondaryLabelForSender($request, $order)
    {

        if ($request->pickupShipment && !$this->setPickupShipment($order, $request)) {
            return false;
        }

        $response = UPSFacade::getLabelForSender($order, $request);

        if($response->success == true)
        {
            $this->totalUpsCost = $this->totalUpsCost + $response->data['ShipmentResponse']['ShipmentResults']['ShipmentCharges']['TotalCharges']['MonetaryValue'];
            $this->addProfitForConslidatedOrder($order['user'], $this->totalUpsCost);

            if($request->exists('consolidated_order'))
            {
                if(!$this->updateConsolidatedOrders($request, $response))
                {
                    return false;
                }

                $this->order = $request->orders->first();
            }else
            {
                
                $order->update([
                    'us_api_response' => json_encode($response->data),
                    'us_api_tracking_code' => $response->data['ShipmentResponse']['ShipmentResults']['ShipmentIdentificationNumber'],
                    'us_secondary_label_cost' => setUSCosts($this->total_ups_cost, $this->total_amount_with_profit),
                    'us_api_service' => $request->service,
                    'api_pickup_response' => ($request->pickupShipment == true) ? $this->pickupResponse : null,
                ]);

                chargeAmount(round($this->total_amount_with_profit, 2), $order, 'Bought UPS Label For : ');
                $order->refresh();

                $this->order = $order;
            }

            $this->convertLabelToPDF($this->order);

            return true;
        }

        $this->upsError = $response->error['response']['errors'][0]['message'];
        return false;
    }

    private function setPickupShipment($order, $request)
    {
        $pickupShipmentresponse = UPSFacade::createPickupShipment($order, $request);
        if ($pickupShipmentresponse->success == false) {
            $this->upsError = $pickupShipmentresponse->error['response']['errors'][0]['message'];
            return false;
        }

        $pickupShipmentCost = $pickupShipmentresponse->data['PickupCreationResponse']['RateResult']['GrandTotalOfAllCharge'];
        $this->addPickupRate($pickupShipmentCost);
        $this->pickupResponse = $pickupShipmentresponse->data;
        return true;
    }

    private function convertLabelToPDF($order)
    {
        $labelPrinter = new UPSLabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->rotatePNGLabel();
        $labelPrinter->saveLabel();
        $labelPrinter->deletePNGLabel();

        return true;
    }

    private function addOrderTracking($order)
    {
        if($order->trackings->isEmpty())
        {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => Order::STATUS_PAYMENT_DONE,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => ($order->user->country != null) ? $order->user->country->code : 'US',
            ]);
        }    

        return true;
    }


    public function getUPSErrors()
    {
        return $this->upsError;
    }

    public function getRatesForSender($request)
    {  
        $order = ($request->exists('consolidated_order')) ? $request->order : Order::find($request->order_id);
        $response = UPSFacade::getSenderPrice($order, $request);

        if($response->success == true)
        {
            $upsRate = $response->data['RateResponse']['RatedShipment']['TotalCharges']['MonetaryValue'];

            ($request->exists('consolidated_order')) ? $this->addProfitForConslidatedOrder($order['user'], $upsRate) 
                                                        : $this->addProfit($order->user, $upsRate);

            if($request->pickupShipment)
            {
                $response = UPSFacade::getPickupRates($request);
                if($response->success == true)
                {
                    $this->addPickupRate($response->data['PickupRateResponse']['RateResult']['GrandTotalOfAllCharge']);
                    
                }else {
                    return (Array)[
                        'success' => false,
                        'message' => 'server error, could not get rates',
                    ];
                }
            }
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

    private function checkUserBalance($charges)
    {
        if ($charges > getBalance())
        {
            $this->upsError = 'Not Enough Balance. Please Recharge your account.';
            return false;
        }

        return true;
    }

    private function addProfit($user, $ups_rates)
    {
        $this->user_api_profit = setting('ups_profit', null, $user->id);

        if($this->user_api_profit == null || $this->user_api_profit == 0)
        {
            $this->user_api_profit = setting('ups_profit', null, 1);
        }

        $profit = $ups_rates * ($this->user_api_profit / 100);

        $this->total_amount_with_profit = $ups_rates + $profit;

        return true;
    }

    private function addPickupRate($pickup_charges)
    {
        $this->total_amount_with_profit = $this->total_amount_with_profit + $pickup_charges;
        return true;
    }

    private function addProfitForConslidatedOrder($user, $upsRate)
    {
        $user = User::find($user['id']);
        return $this->addProfit($user, $upsRate);
    }

    private function updateConsolidatedOrders($request, $response)
    {
        DB::transaction(function () use ($request, $response) {
            try {

                foreach ($request->orders as $order) {
                    $order->update([
                        'us_api_response' => json_encode($response->data),
                        'us_api_tracking_code' => $response->data['ShipmentResponse']['ShipmentResults']['ShipmentIdentificationNumber'],
                        'us_secondary_label_cost' => setUSCosts($this->totalUpsCost, $this->total_amount_with_profit),
                        'us_api_service' => $request->service,
                        'api_pickup_response' => ($request->pickupShipment == true) ? $this->pickupResponse : null,
                    ]);
    
                    chargeAmount(round($this->total_amount_with_profit, 2), $order, 'Bought UPS Label For : ');

                    $order->refresh();
                }

                return true;

            } catch (\Exception $ex) {
                Log::error($ex->getMessage());
                $this->upsError = $ex->getMessage();
                return false;
            }
            
        });

        return true;
    }

    public function getShippingServices($order)
    {
        $shippingServices = collect();

        $ups_shippingService = new UPSShippingService($order);
        foreach (ShippingService::query()->active()->get() as $shippingService) {
            if ( $ups_shippingService->isAvailableFor($shippingService) ){
                    $shippingServices->push($shippingService);
            }
        }

        if($shippingServices->isEmpty())
        {
            $this->upsError = 'No shipping services available for this order';
        }

        if($shippingServices->isNotEmpty() && !setting('ups', null, $order->user->id))
        {
            $this->upsError = "UPS is not enabled for your account";
            $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                return $shippingService->service_sub_class != ShippingService::UPS_GROUND;
            });
        }

        return $shippingServices;
    }
}