<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Order;
use App\Facades\FedExFacade;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

        if($shippingServices->isNotEmpty() && !setting('fedex', null, $order->user->id))
        {
            $this->fedExError = 'FedEx is not enabled for your account';
            $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                return $shippingService->service_sub_class != ShippingService::FEDEX_GROUND;
            });
        }

        return $shippingServices;
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
            $this->downloadFedexLabel(json_decode($order->api_response), $order->corrios_tracking_code);

            return true;
        }

        $this->fedExError = $response->error['errors'][0]['message'] ?? 'Unknown error';
    }

    public function getSecondaryLabel($request, $order)
    {
        if($this->checkUserBalance($request->total_price))
        {
            if($this->getSecondaryLabelForSender($request, $order))
            {
                return true;
            }

            return false;
        }
    }

    private function getSecondaryLabelForSender($request, $order)
    {
        if ($request->pickupShipment) {
            $pickupShipmentresponse = FedExFacade::createPickupShipment($request);
            
            if ($pickupShipmentresponse->success == false) {
                $this->fedExError = $pickupShipmentresponse->error['errors'][0]['message'] ?? 'Pickup shipment not available';
                return false;
            }

            $this->pickupResponse = $pickupShipmentresponse->data;
        }

        $response = FedExFacade::createShipmentForSender($order, $request);
        
        if ($response->success == true) {
            $this->totalFedExCost = $response->data['output']['transactionShipments'][0]['pieceResponses'][0]['baseRateAmount'];
            ($request->exists('consolidated_order')) ? $this->addProfitForConslidatedOrder($order['user'], $this->totalFedExCost) 
                                                        : $this->addProfit($order->user, $this->totalFedExCost);
            if ($request->exists('consolidated_order')) 
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
                    'us_api_tracking_code' => $response->data['output']['transactionShipments'][0]['pieceResponses'][0]['trackingNumber'],
                    'us_secondary_label_cost' => setUSCosts($this->totalFedExCost, $this->totalAmountWithProfit),
                    'us_api_service' => $request->service,
                    'api_pickup_response' => ($request->pickupShipment == true) ? $this->pickupResponse : null,
                ]);
    
                chargeAmount(round($this->totalAmountWithProfit, 2), $order, 'Bought FedEx Label For : '.$order->warehouse_number);
    
                $order->refresh();
                $this->order = $order;
            }

            
            $this->downloadFedexLabel($this->order->getUSLabelResponse(), $this->order->us_api_tracking_code);
            return true;
        }

        $this->fedExError = $response->error['errors'][0]['message'] ?? 'Unknown error' ;
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

    private function downloadFedexLabel($fedExResponse, $fedExtrackingCode)
    {
        $labelUrl = $fedExResponse->output->transactionShipments[0]->pieceResponses[0]->packageDocuments[0]->url;
        $contents = file_get_contents($labelUrl);
        Storage::put("labels/{$fedExtrackingCode}.pdf", $contents);
        
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

    private function updateConsolidatedOrders($request, $response)
    {
        DB::transaction(function () use ($request, $response) {
            try {
                foreach ($request->orders as $order) {
                    $order->update([
                        'us_api_response' => json_encode($response->data),
                        'us_api_tracking_code' => $response->data['output']['transactionShipments'][0]['pieceResponses'][0]['trackingNumber'],
                        'us_secondary_label_cost' => setUSCosts($this->totalFedExCost, $this->totalAmountWithProfit),
                        'us_api_service' => $request->service,
                        'api_pickup_response' => ($request->pickupShipment == true) ? $this->pickupResponse : null,
                    ]);

                    $order->refresh();
                }

            } catch (\Exception $ex) {
                Log::error($ex->getMessage());
                $this->fedExError = $ex->getMessage();
                return false;
            }
        });

        chargeAmount(round($this->totalAmountWithProfit, 2), $request->orders->first(), 'Bought USPS Label For '.$this->getOrderIds($request->orders));

        return true;
    }

    private function getOrderIds($orders)
    {
        $warehouse_numbers = [];
        foreach ($orders as $order) {
            $warehouse_numbers[] = $order->warehouse_number;
        }

        return implode(' :,', $warehouse_numbers);
    }
}
