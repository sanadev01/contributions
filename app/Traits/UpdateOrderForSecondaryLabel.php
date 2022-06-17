<?php

namespace App\Traits;

trait UpdateOrderForSecondaryLabel
{

    public function updateOrder($order, $responseData, $trackingNumber, $apiCost, $amountToCharge, $service, $pickupResponse = null)
    {
        $order->update([
            'us_api_response' => json_encode($responseData),
            'us_api_tracking_code' => $trackingNumber,
            'us_secondary_label_cost' => setUSCosts($apiCost, $amountToCharge),
            'us_api_service' => $service,
            'api_pickup_response' => ($pickupResponse) ? $pickupResponse : null,
        ]);
        
        $order->refresh();
    }
}