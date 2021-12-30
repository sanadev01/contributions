<?php


namespace App\Repositories;


use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use App\Facades\UPSFacade;
use App\Facades\USPSFacade;
use App\Models\OrderTracking;
use App\Models\PaymentInvoice;
use App\Models\ShippingService;
use App\Services\UPS\UPSLabelMaker;
use Illuminate\Support\Facades\Auth;
use App\Services\USPS\USPSLabelMaker;
use App\Services\UPS\UPSShippingService;
use App\Services\USPS\USPSShippingService;


class UPSLabelRepository
{
    protected $shipping_service_code;
    public $ups_errors;
    protected $user_api_profit;
    protected $total_amount;

    public function handle($order)
    {
        if($order->api_response == null)
        {
            $this->generatUPSLabel($order);

        }

        return true;
    }

    public function buyLabel($request, $order)
    {   
        if ( $request->total_price > getBalance())
        {
            $this->ups_errors = 'Not Enough Balance. Please Recharge your account.';
           
            return false;
        }

        // Buy second label(domestic ups label)
        $this->buy_UPS_label($order, $request);

        return true;
    }

    public function buy_UPS_label($order, $request)
    {
        $request->merge(['sender_phone' => $order->user->phone]);
        $response = UPSFacade::buyLabel($order, $request);

        if($response->success == true)
        {
            $this->addProfit($order->user, $response->data['ShipmentResponse']['ShipmentResults']['ShipmentCharges']['TotalCharges']['MonetaryValue']);

            if($request->has('pickup'))
            {
                $pickupShipmentresponse = UPSFacade::createPickupShipment($order, $request);
                
                if ($pickupShipmentresponse->success == true) {
                    $this->addPickupRate($pickupShipmentresponse->data['PickupCreationResponse']['RateResult']['GrandTotalOfAllCharge']);

                    $order->update([
                        'api_pickup_response' => $pickupShipmentresponse->data,
                    ]);
                    
                }else 
                {
                    $this->ups_errors = $pickupShipmentresponse->error['response']['errors'][0]['message'];
                    return false;
                }
            }
            /**
                * Note...
                * us_api_tracking_code and us_api_response are the columns for 
                * domestic label(when there is a second label against order(UPS or USPS Label)
            */
            
            $order->update([
                'us_api_response' => json_encode($response->data),
                'us_api_tracking_code' => $response->data['ShipmentResponse']['ShipmentResults']['ShipmentIdentificationNumber'],
                'us_api_cost' => $this->total_amount,
                'us_api_service' => $request->service,
            ]);

            chargeAmount(round($this->total_amount, 2), $order, 'Bought UPS Label For : ');

            $this->convertLabelToPDF($order);

            return true;
        }

        $this->ups_errors = $response->error['response']['errors'][0]['message'];

        return;
    }

    public function update($order)
    {
        $this->generatUPSLabel($order);
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

        $this->ups_errors = $response->error['response']['errors'][0]['message'];
        return false;
    }

    private function generatUPSLabel($order)
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

            $this->ups_errors = $response->error['response']['errors'][0]['message'];
            return null;
        }
        
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
        return $this->ups_errors;
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
            $this->ups_errors = 'No shipping services available for this order';
        }

        if($shippingServices->isNotEmpty() && !setting('ups', null, $order->user->id))
        {
            $this->ups_errors = "UPS is not enabled for your account";
            $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                return $shippingService->service_sub_class != ShippingService::UPS_GROUND;
            });
        }

        return $shippingServices;
    }

    public function getRates($request)
    {
        $order = Order::find($request->order_id);
        $response = UPSFacade::getSenderPrice($order, $request);

        if($response->success == true)
        {
            $this->addProfit($order->user, $response->data['RateResponse']['RatedShipment']['TotalCharges']['MonetaryValue']);

            if($request->pickup == "true")
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
                'total_amount' => round($this->total_amount, 2),
            ]; 
        }

        return (Array)[
            'success' => false,
            'message' => 'server error, could not get rates',
        ]; 
    }

    private function addProfit($user, $ups_rates)
    {
        $this->user_api_profit = setting('ups_profit', null, $user->id);

        if($this->user_api_profit == null || $this->user_api_profit == 0)
        {
            $this->user_api_profit = setting('ups_profit', null, 1);
        }

        $profit = $ups_rates * ($this->user_api_profit / 100);

        $this->total_amount = $ups_rates + $profit;

        return true;
    }

    private function addPickupRate($pickup_charges)
    {
        $this->total_amount = $this->total_amount + $pickup_charges;
        return true;
    }
}