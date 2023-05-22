<?php


namespace App\Repositories;


use App\Models\User;
use App\Models\Order;
use App\Facades\UPSFacade;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use Illuminate\Pipeline\Pipeline;
use App\Errors\SecondaryLabelError;
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
    protected $totalPickupCost = 0;
    protected $pickupResponse;

    public $order;

    public function run(Order $order,$update)
    {
        if($update){
            return $this->update($order);
        }
        else {
            return $this->handle($order);
        }
    }
    
    public function handle($order)
    {
        if($order->api_response == null && $order->shippingService->service_sub_class == ShippingService::UPS_GROUND)
        {
            $this->getPrimaryLabel($order);
        }

        if ($order->api_response) {
            $this->convertLabelToPDF($order);
        }
        return true;
    }

    public function update($order)
    {
        $this->getPrimaryLabel($order);
    }

    public function getSecondaryLabel($order)
    {
        $approximateCost = request()->total_price + 5;
        $order = app(Pipeline::class)
                ->send($order)
                ->through([
                    'App\Pipes\ValidateUserBalance:'.$approximateCost,
                    'App\Pipes\SecondaryLabel\UPSLabel'
                ])
                ->thenReturn();

        if($order instanceof SecondaryLabelError){
            $this->upsError = $order->getError();
            return false;
        }
        
        $this->convertLabelToPDF($order);
        return true;
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

    private function getPrimaryLabel($order)
    {
        $response = UPSFacade::getLabelForRecipient($order);
        
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

    public function getPrimaryLabelForSender($order, $request)
    {
        $response = UPSFacade::getLabelForSender($order, $request);

        if ($response->success == true) {
            $this->handleApiResponse($response, $order);
            return true;
        }

        $this->upsError = $response->error['response']['errors'][0]['message'];
        return false;
    }

    public function getPrimaryLabelForRecipient($order)
    {
        $response = UPSFacade::getLabelForRecipient($order);
        
        if ($response->success == true) {
            $this->handleApiResponse($response, $order);
            return true;
        }

        $this->upsError = $response->error['response']['errors'][0]['message'];
        return false;
    }

    private function handleApiResponse($response, $order)
    {
        $this->totalUpsCost += $response->data['ShipmentResponse']['ShipmentResults']['ShipmentCharges']['TotalCharges']['MonetaryValue'];
        $this->addProfit($order->user);

        $order->update([
            'api_response' => json_encode($response->data),
            'corrios_tracking_code' => $response->data['ShipmentResponse']['ShipmentResults']['ShipmentIdentificationNumber'],
            'is_invoice_created' => true,
            'is_shipment_added' => true,
            'user_declared_freight' => $this->totalUpsCost,
            'shipping_value' => $this->total_amount_with_profit,
            'total' => $this->total_amount_with_profit,
            'gross_total' => $this->total_amount_with_profit,
            'status' => Order::STATUS_PAYMENT_DONE,
        ]);

        $order->refresh();

        // store order status in order tracking
        $this->addOrderTracking($order);

        // Connert PNG label To PDF
        $this->convertLabelToPDF($order);

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

    public function getRatesForSender()
    {  
        $order = (request()->exists('consolidated_order') && request()->consolidated_order == true) ? request()->order : Order::find(request()->order_id);
        $response = UPSFacade::getSenderRates($order, request());

        if($response->success == true)
        {
            $this->totalUpsCost += $response->data['RateResponse']['RatedShipment']['TotalCharges']['MonetaryValue'];

            if(request()->pickupShipment)
            {
                $response = UPSFacade::getPickupRates(request());
                if($response->success == true)
                {
                    $this->totalPickupCost += $response->data['PickupRateResponse']['RateResult']['GrandTotalOfAllCharge'];
                    
                }else {
                    return (Array)[
                        'success' => false,
                        'message' => 'server error, could not get rates',
                    ];
                }
            }
            (request()->exists('consolidated_order')) ? $this->addProfitForConslidatedOrder($order['user']) 
                                                        : $this->addProfit($order->user);
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

    private function addProfit($user)
    {
        $this->user_api_profit = setting('ups_profit', null, $user->id);

        if($this->user_api_profit == null || $this->user_api_profit == 0)
        {
            $this->user_api_profit = setting('ups_profit', null, User::ROLE_ADMIN);
        }

        $ups_rates = $this->totalUpsCost + $this->totalPickupCost;
        
        $profit = $ups_rates * ($this->user_api_profit / 100);
        $this->total_amount_with_profit += $ups_rates + $profit;
        return true;
    }


    private function addProfitForConslidatedOrder($user)
    {
        $user = User::find($user['id']);
        return $this->addProfit($user);
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

        if($shippingServices->isNotEmpty() && !setting('ups', null, User::ROLE_ADMIN))
        {
            $this->upsError = "UPS is not enabled for your account";
            $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                return $shippingService->service_sub_class != ShippingService::UPS_GROUND;
            });
        }

        return $shippingServices;
    }
}