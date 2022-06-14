<?php

return [
    'testing' => [
        'credentials' => [
            'client_id' => 'l7e9ac2a1c19254a42a8ec17a8683a8c3a',
            'client_secret' => '454c469100f449d3ab5a15da0f77b6dc',
            'account_number' => '510087020'
        ],
        'getTokenUrl' => 'https://apis-sandbox.fedex.com/oauth/token',
        'getRatesUrl' => 'https://apis-sandbox.fedex.com/rate/v1/rates/quotes',
        'createShipmentUrl' => 'https://apis-sandbox.fedex.com/ship/v1/shipments',
        'createPickupUrl' => 'https://apis-sandbox.fedex.com/pickup/v1/pickups',
        'cancelPickupUrl' => 'https://apis-sandbox.fedex.com/pickup/v1/pickups/cancel',
    ],
    'production' => [
        'credentials' => [
            'client_id' => 'l72227acb50e2b425f96d2dcfa5a36c551',
            'client_secret' => '1e962fbc-c778-42b0-bb15-f619496ec189',
            'account_number' => '109740748'
        ],
        'getTokenUrl' => 'https://apis.fedex.com/oauth/token',
        'getRatesUrl' => 'https://apis.fedex.com/rate/v1/rates/quotes',
        'createShipmentUrl' => 'https://apis.fedex.com/ship/v1/shipments',
        'createPickupUrl' => 'https://apis.fedex.com/pickup/v1/pickups',
        'cancelPickupUrl' => 'https://apis.fedex.com/pickup/v1/pickups/cancel',
    ],
];