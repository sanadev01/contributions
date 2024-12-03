<?php

return [
    'testing' => [
        'credentials' => [
            'client_id' => env('TEST_FEDEX_CLIENT_ID'),
            'client_secret' => env('TEST_FEDEX_CLIENT_SECRET'),
            'account_number' => env('TEST_FEDEX_ACCOUNT_NUMBER')
        ],
        'getTokenUrl' => 'https://apis-sandbox.fedex.com/oauth/token',
        'getRatesUrl' => 'https://apis-sandbox.fedex.com/rate/v1/rates/quotes',
        'createShipmentUrl' => 'https://apis-sandbox.fedex.com/ship/v1/shipments',
        'createPickupUrl' => 'https://apis-sandbox.fedex.com/pickup/v1/pickups',
        'cancelPickupUrl' => 'https://apis-sandbox.fedex.com/pickup/v1/pickups/cancel',
    ],
    'production' => [
        'credentials' => [            
            'client_id' => env('PROD_FEDEX_CLIENT_ID'),
            'client_secret' => env('PROD_FEDEX_CLIENT_SECRET'),
            'account_number' => env('PROD_FEDEX_ACCOUNT_NUMBER')
        ],
        'getTokenUrl' => 'https://apis.fedex.com/oauth/token',
        'getRatesUrl' => 'https://apis.fedex.com/rate/v1/rates/quotes',
        'createShipmentUrl' => 'https://apis.fedex.com/ship/v1/shipments',
        'createPickupUrl' => 'https://apis.fedex.com/pickup/v1/pickups',
        'cancelPickupUrl' => 'https://apis.fedex.com/pickup/v1/pickups/cancel',
    ],
];