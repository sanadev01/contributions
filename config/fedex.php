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
        'client_id' => 'l7bc13fa29ecab4417919ace5e6a089171',
        'client_secret' => '1ea88a9850d24218bc4ca047a989e42d',
        'account_number' => '510087020'
    ]
];