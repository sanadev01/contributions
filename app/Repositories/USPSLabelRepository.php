<?php


namespace App\Repositories;

use App\Models\User;
use App\Models\Order;
use App\Facades\USPSFacade;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\USPS\USPSLabelMaker;
use App\Services\USPS\USPSShippingService;


class USPSLabelRepository
{
    protected $uspsError;
    public $user_api_profit;
    public $total_amount_with_profit;
    public $order;

    public $totalUspsCost = 0;

    public function handle($order)
    {
        if($order->isPaid() && !$order->api_response)
        {
    
            $this->getPrimaryLabel($order);

        }elseif($order->api_response != null)
        {
            
            $this->printPrimaryLabel($order);
        }
    }

    public function update($order)
    {
        $this->getPrimaryLabel($order);
    }

    public function getPrimaryLabel($order)
    {
        $response = USPSFacade::getPrimaryLabelForRecipient($order);

        if($response->success == true)
        {
            //storing response in orders table
            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data['usps']['tracking_numbers'][0],
            ]);
            // store order status in order tracking
            $this->addOrderTracking($order);

            $this->printPrimaryLabel($order);

        } else {

            $this->uspsError = $response->message;
            return null;
        }
        
    }

    public function getPrimaryLabelForSender($order, $request)
    {
        $response = USPSFacade::getLabelForSender($order, $request);

        if($response->success == true) 
        {
            $this->totalUspsCost += $response->data['total_amount'] ?? 0;
            $this->addProfit($order->user, $this->totalUspsCost);

            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data['usps']['tracking_numbers'][0] ?? null,
                'is_invoice_created' => true,
                'is_shipment_added' => true,
                'user_declared_freight' => $this->totalUspsCost,
                'shipping_value' => $this->total_amount_with_profit,
                'total' => $this->total_amount_with_profit,
                'gross_total' => $this->total_amount_with_profit,
                'status' => Order::STATUS_PAYMENT_DONE,
            ]);

            $order->refresh();

            // store order status in order tracking
            $this->addOrderTracking($order);

            $this->printPrimaryLabel($order);

            return true;
        }

        $this->uspsError = $response->message;
        return false;
    }

    public function printPrimaryLabel(Order $order)
    {
        $labelPrinter = new USPSLabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveLabel();

        return true;
    }

    public function getUSPSErrors()
    {
        return $this->uspsError;
    }

    public function addOrderTracking($order)
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
    
    public function getShippingServices($order)
    {
        $shippingServices = collect();

        $usps_shippingService = new USPSShippingService($order);

        foreach (ShippingService::query()->active()->get() as $shippingService) {
            if ( $usps_shippingService->isAvailableFor($shippingService) ){
                $shippingServices->push($shippingService);
            }
        }

        if($shippingServices->isEmpty())
        {
            $this->ups_errors = 'No shipping services available for this order';
        }
        
        if($shippingServices->contains('service_sub_class', ShippingService::USPS_PRIORITY) || $shippingServices->contains('service_sub_class', ShippingService::USPS_FIRSTCLASS))
        {
            if(!setting('usps', null, $order->user->id))
            {
                $this->uspsError = "USPS is not enabled for your account";
                $shippingServices = collect() ;
            }
        }
        
        return $shippingServices;

        
    }

    public function getRatesForSender($request)
    {
        $order = ($request->exists('consolidated_order') && $request->consolidated_order == true) ? $request->order : Order::find($request->order_id);
        $response = USPSFacade::getSenderPrice($order, $request);

        if($response->success == true)
        {
            $uspsRate = $response->data['total_amount'];
            
            ($request->exists('consolidated_order')) ? $this->addProfitForConslidatedOrder($order['user'], $uspsRate) 
                                                        : $this->addProfit($order->user, $uspsRate);

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

    public function getSecondaryLabel($request, $order)
    {
        if($this->checkUserBalance($request->total_price))
        {
            if($this->getSecondaryLabelForSender($request, $order))
            {
                return true;
            }
        }

        return false;
    }

    public function getSecondaryLabelForSender($request, $order)
    {
        $response = USPSFacade::getLabelForSender($order, $request);
        
        if($response->success == true) 
        {
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
                    'us_api_tracking_code' => $response->data['usps']['tracking_numbers'][0],
                    'us_secondary_label_cost' => setUSCosts($response->data['total_amount'], $request->total_price),
                    'us_api_service' => $request->service,
                ]);
    
                chargeAmount($request->total_price, $order, 'Bought USPS Label For : '.$order->warehouse_number);
                $order->refresh();
                $this->order = $order;
            }

            $this->printSecondaryLabel($this->order);

            return true;
        }

        $this->uspsError = $response->message;
        return false;
    }

    private function addProfit($user, $usps_rate)
    {
        $this->user_api_profit = setting('usps_profit', null, $user->id);

        if($this->user_api_profit == null || $this->user_api_profit == 0)
        {
            $this->user_api_profit = setting('usps_profit', null, 1);
        }

        $profit = $usps_rate * ($this->user_api_profit / 100);

        $this->total_amount_with_profit = $usps_rate + $profit;

        return true;
    }

    private function addProfitForConslidatedOrder($user, $uspsRate)
    {
        $user = User::find($user['id']);
        return $this->addProfit($user, $uspsRate);
    }

    private function checkUserBalance($charges)
    {
        if ($charges > getBalance())
        {
            $this->uspsError = 'Not Enough Balance. Please Recharge your account.';
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
                        'us_api_tracking_code' => $response->data['usps']['tracking_numbers'][0],
                        'us_secondary_label_cost' => setUSCosts($response->data['total_amount'], $request->total_price),
                        'us_api_service' => $request->service,
                    ]);

                    $order->refresh();
                }

                return true;

            } catch (\Exception $ex) {
                Log::error($ex->getMessage());
                $this->uspsError = $ex->getMessage();
                return false;
            }
            
        });

        chargeAmount($request->total_price, $request->orders->first(), 'Bought USPS Label For '.$this->getOrderIds($request->orders));

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

    private function printSecondaryLabel(Order $order)
    {
        $labelPrinter = new USPSLabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveSecondaryLabel();

        return true;
    }
    
}