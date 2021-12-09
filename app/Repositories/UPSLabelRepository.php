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
    public $ups_errors;

    public function handle($order)
    {
        if($order->api_response == null)
        {
            $this->generatUPSLabel($order);

        }

        return true;
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
}