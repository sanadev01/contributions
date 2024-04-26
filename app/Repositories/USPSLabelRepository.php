<?php


namespace App\Repositories;

use App\Models\User;
use App\Models\Order;
use App\Facades\USPSFacade;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use Illuminate\Pipeline\Pipeline;
use App\Errors\SecondaryLabelError;
use App\Services\USPS\Services\UpdateCN23Label;
use App\Services\USPS\USPSLabelMaker;
use App\Services\USPS\USPSShippingService;


class USPSLabelRepository
{
    protected $uspsError;
    public $user_api_profit;
    public $total_amount_with_profit;
    public $order;

    public $totalUspsCost = 0;
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
            $this->handleApiResponse($response, $order);

            return true;
        }

        $this->uspsError = $response->message;
        return false;
    }

    public function getPrimaryLabelForRecipient($order)
    {
        $response = USPSFacade::getPrimaryLabelForRecipient($order);

        if($response->success == true) 
        {
            $this->handleApiResponse($response, $order);

            return true;
        }

        $this->uspsError = $response->message;
        return false;
    }

    private function handleApiResponse($response, $order)
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

    private function printPrimaryLabel(Order $order)
    {
        $usps_response = json_decode($order->api_response);
        $base64_pdf = $usps_response->base64_labels[0];
        $fileName = $order->corrios_tracking_code;

        $labelPrinter = new USPSLabelMaker();
        $labelPrinter->saveLabel($base64_pdf, $fileName);
        if($order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL) {
            return (new UpdateCN23Label($order))->run(); 
        }
        return true;
    }

    public function getUSPSErrors()
    {
        return $this->uspsError;
    }    
    public function getError()
    {
        return $this->uspsError;
    }
    public function getError()
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
            if(!setting('usps', null, User::ROLE_ADMIN))
            {
                $this->uspsError = "USPS is not enabled for your account";
                $shippingServices = collect() ;
            }
        }
        
        return $shippingServices;

        
    }

    public function getRatesForSender()
    {
        $order = (request()->exists('consolidated_order') && request()->consolidated_order == true) ? request()->order : Order::find(request()->order_id);
        $response = USPSFacade::getSenderRates($order, request());

        if($response->success == true)
        {
            $uspsRate = $response->data['total_amount'];
            
            (request()->exists('consolidated_order')) ? $this->addProfitForConslidatedOrder($order['user'], $uspsRate) 
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

    public function getSecondaryLabel($order)
    {
        $order = app(Pipeline::class)
                ->send($order)
                ->through([
                    'App\Pipes\ValidateUserBalance:'.request()->total_price,
                    'App\Pipes\SecondaryLabel\USPSLabel',
                ])
                ->thenReturn();
        
        if($order instanceof SecondaryLabelError){
            $this->uspsError = $order->getError();
            return false;
        }

        $this->printSecondaryLabel($order);
        return true;
    }

    private function addProfit($user, $usps_rate)
    {
        $this->user_api_profit = setting('usps_profit', null, $user->id);

        if($this->user_api_profit == null || $this->user_api_profit == 0)
        {
            $this->user_api_profit = setting('usps_profit', null, User::ROLE_ADMIN);
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

    private function printSecondaryLabel(Order $order)
    {
        $usps_response = json_decode($order->us_api_response);
        $base64_pdf = $usps_response->base64_labels[0];
        $fileName = $order->us_api_tracking_code;

        $labelPrinter = new USPSLabelMaker();
        $labelPrinter->saveLabel($base64_pdf, $fileName);

        return true;
    }
    
}