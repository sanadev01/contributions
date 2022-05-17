<?php

return [
    'testing' => [
        'getTokenUrl' => 'https://apis-sandbox.fedex.com/oauth/token',
        'getRatesUrl' => 'https://apis-sandbox.fedex.com/rate/v1/rates/quotes',
        'createShipmentUrl' => 'https://apis-sandbox.fedex.com/ship/v1/shipments',
        'createPickupUrl' => 'https://apis-sandbox.fedex.com/pickup/v1/pickups',
        'cancelPickupUrl' => 'https://apis-sandbox.fedex.com/pickup/v1/pickups/cancel',
    ],
    'production' => [
        'getTokenUrl' => '',
        'getRatesUrl' => '',
        'createShipmentUrl' => '',
        'createPickupUrl' => '',
        'cancelPickupUrl' => '',
    ],
    'credentials' => [
        'client_id' => 'l7e9ac2a1c19254a42a8ec17a8683a8c3a',
        'client_secret' => '454c469100f449d3ab5a15da0f77b6dc',
        'account_number' => '510087020'
    ]
];