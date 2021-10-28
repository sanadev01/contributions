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
use App\Services\USPS\USPSShippingService;


class UPSLabelRepository
{
    protected $shipping_service_code;

    public function handle($order)
    {
        if($order->api_response == null)
        {
            $this->generatUPSLabel($order);

        }elseif ($order->api_response != null)
        {
            $this->printLabel($order);
        }
    }

    public function update($order)
    {
        $this->generatUPSLabel($order);
    }

    private function generatUPSLabel($order)
    {
        $response = UPSFacade::generateLabel($order);

        if($response->success == true)
        {
            //storing response in orders table
            $order->update([
                'api_response' => json_encode($response->data),
                'corrios_tracking_code' => $response->data['FreightShipResponse']['ShipmentResults']['ShipmentNumber'],
            ]);
            // store order status in order tracking
            $this->addOrderTracking($order);

            $this->printLabel($order);

            return true;

        } else {

            $this->usps_errors = $response->error['response']['errors'][0]['message'];
            return null;
        }
        
    }

    private function printLabel(Order $order)
    {
        $labelPrinter = new UPSLabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveLabel();

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
        # code... 03129427
    }
}