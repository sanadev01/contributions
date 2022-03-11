<?php

namespace App\Services\FedEx;

use App\Models\ShippingService;

class ConsolidatedOrderService
{
    protected $accountNumber;

    public function handle($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    public function makeRequestForSenderRates($order, $request)
    {
        return [
            'accountNumber' => [
                'value' => $this->accountNumber,
            ],
            'requestedShipment' => [
                'shipper' => [
                    'address' => [
                        'city' => $request->sender_city,
                        'stateOrProvinceCode' => $request->sender_state,
                        'postalCode' => $request->sender_zipcode,
                        'countryCode' => 'US',
                    ]
                ],
                'recipient' => [
                    'address' => [
                        'city' => 'Miami',
                        'stateOrProvinceCode' => 'FL',
                        'postalCode' => 33182,
                        'countryCode' => 'US',
                    ]
                ],
                'serviceType' => ($request->service == ShippingService::FEDEX_GROUND) ? 'FEDEX_GROUND' : 'GROUND_HOME_DELIVERY',
                'pickupType' => ($request->pickup == "true") ? 'CONTACT_FEDEX_TO_SCHEDULE' : 'DROPOFF_AT_FEDEX_LOCATION',
                'rateRequestType' => [
                    'ACCOUNT'
                ],
                'requestedPackageLineItems' => [
                    [
                        'weight' => [
                            'units' => 'KG',
                            'value' => (float)$order['weight'],
                        ]
                    ]
                ]
            ],
        ];
    }
}
