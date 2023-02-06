<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Order;
use App\Facades\FedExFacade;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use Illuminate\Pipeline\Pipeline;
use App\Errors\SecondaryLabelError;
use Illuminate\Support\Facades\Log;
use App\Services\FedEx\FedExLabelMaker;
use App\Services\FedEx\FedExShippingService;

class FedExLabelRepository
{
    public $fedExError;
    protected $userApiProfit;
    protected $totalAmountWithProfit;
    protected $totalFedExCost;
    protected $pickupResponse;

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

        if($shippingServices->isNotEmpty() && !setting('fedex', null, User::ROLE_ADMIN))
        {
            $this->fedExError = 'FedEx is not enabled for your account';
            $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                return $shippingService->service_sub_class != ShippingService::FEDEX_GROUND;
            });
        }

        return $shippingServices;
    }

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
        if(!$order->api_response)
        {
           return $this->getPrimaryLabel($order);
        }

        return true;
    }

    public function update($order)
    {
        if($order->isPaid())
        {
            return $this->getPrimaryLabel($order);
        }
    }

    public function getPrimaryLabel($order)
    {
        $response = FedExFacade::createShipmentForRecipient($order);

        if ($response->success == true) {

            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data['output']['transactionShipments'][0]['pieceResponses'][0]['trackingNumber'],
            ]);

            $order->refresh();
            $this->printLabel($order->api_response, $order->corrios_tracking_code);

            // store order status in order tracking
            $this->addOrderTracking($order);

            return true;
        }

        $this->fedExError = $response->error['errors'][0]['code'].'-'.$response->error['errors'][0]['message'] ?? 'Unknown error';
    }

    public function getSecondaryLabel($order)
    {
        $order = app(Pipeline::class)
                ->send($order)
                ->through([
                    'App\Pipes\ValidateUserBalance:'.request()->total_price,
                    'App\Pipes\SecondaryLabel\FedExLabel',
                ])->thenReturn();
        
        
        if($order instanceof SecondaryLabelError){
            $this->uspsError = $order->getError();
            return false;
        }

        $this->printLabel($order->us_api_response, $order->us_api_tracking_code);
        return true;
    }

    public function getPrimaryLabelForSender($order, $request)
    {
        $response = FedExFacade::createShipmentForSender($order, $request);

        if ($response->success == true) {
            $this->handleApiResponse($response, $order);

            return true;
        }

        $this->fedExError = $response->error['errors'][0]['code'].'-'.$response->error['errors'][0]['message'] ?? 'Unknown error';
        return false;
    }

    public function getPrimaryLabelForRecipient($order)
    {
        $response = FedExFacade::createShipmentForRecipient($order);

        if ($response->success == true) {
            $this->handleApiResponse($response, $order);

            return true;
        }

        $this->fedExError = $response->error['errors'][0]['message'] ?? 'Unknown error';
        return false;
    }
    
    public function getRatesForSender($request)
    {
        $order = ($request->exists('consolidated_order') && $request->consolidated_order == true) ? $request->order : Order::find($request->order_id);
        $response = FedExFacade::getSenderRates($order, $request);

        if($response->success == true)
        {
            $fedExRate = $response->data['output']['rateReplyDetails'][0]['ratedShipmentDetails'][0]['totalNetFedExCharge'];
            
            ($request->exists('consolidated_order')) ? $this->addProfitForConslidatedOrder($order['user'], $fedExRate) 
                                                        : $this->addProfit($order->user, $fedExRate);

            
            return (Array)[
                'success' => true,
                'total_amount' => round($this->totalAmountWithProfit, 2),
            ];
        }
        return (Array)[
            'success' => false,
            'message' => $response->error['errors'][0]['message'] ?? 'Unknown error',
        ]; 
    }

    private function addProfit($user, $fedExRate)
    {
        $this->userApiProfit = setting('fedex_profit', null, $user->id);

        if($this->userApiProfit == null || $this->userApiProfit == 0)
        {
            $this->userApiProfit = setting('fedex_profit', null, User::ROLE_ADMIN);
        }

        $profit = $fedExRate * ($this->userApiProfit / 100);

        $this->totalAmountWithProfit = $fedExRate + $profit;
        return true;
    }

    public function getFedExErrors()
    {
        return $this->fedExError;
    }

    private function printLabel($apiResponse, $trackingCode)
    {
        $labelMaker = new FedExLabelMaker($apiResponse, $trackingCode);
        if($labelMaker->convertLabelToPdf()){
            $labelMaker->saveLabel();
        }

        return true;
    }

    private function addProfitForConslidatedOrder($user, $fedexRate)
    {
        $user = User::find($user['id']);
        return $this->addProfit($user, $fedexRate);
    }

    private function checkUserBalance($charges)
    {
        if ($charges > getBalance())
        {
            $this->fedExError = 'Not Enough Balance. Please Recharge your account.';
            return false;
        }

        return true;
    }

    private function handleApiResponse($response, $order)
    {
        $this->totalFedExCost = $response->data['output']['transactionShipments'][0]['pieceResponses'][0]['baseRateAmount'];
        $this->addProfit($order->user, $this->totalFedExCost);

        $order->update([
            'api_response' => json_encode($response->data),
            'corrios_tracking_code' => $response->data['output']['transactionShipments'][0]['pieceResponses'][0]['trackingNumber'],
            'is_invoice_created' => true,
            'is_shipment_added' => true,
            'user_declared_freight' => $this->totalFedExCost,
            'shipping_value' => round($this->totalAmountWithProfit, 2),
            'total' => round($this->totalAmountWithProfit, 2),
            'gross_total' => round($this->totalAmountWithProfit, 2),
            'status' => Order::STATUS_PAYMENT_DONE,
        ]);

        $order->refresh();

        // store order status in order tracking
        $this->addOrderTracking($order);

        $this->printLabel($order->api_response, $order->corrios_tracking_code);
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
}
