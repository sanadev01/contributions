<?php

return [
    'testing' => [
        'getTokenUrl' => 'https://apis-sandbox.fedex.com/oauth/token',
        'getRatesUrl' => 'https://apis-sandbox.fedex.com/rate/v1/rates/quotes',
        'createShipmentUrl' => 'https://apis-sandbox.fedex.com/ship/v1/shipments',
    ],
    'production' => [
        'getTokenUrl' => '',
        'getRatesUrl' => '',
        'createShipmentUrl' => '',
    ],
    'credentials' => [
        'client_id' => 'l70a73c2c8c5124eb1a7c309b7d6b47649',
        'client_secret' => '33c94c3e3af143a7bcf9ff11a5147919',
        'account_number' => '510087020'
    ]
];