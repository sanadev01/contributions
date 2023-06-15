<?php

return[
    'production' => [
        'baseUrl' => '',
    ],
    'testing' => [
        'baseUrl' => 'https://api.mileexpress.com.br',
    ],
    'credentials' => [
        'grant_type' => 'password',
        'clientId' => 4932,
        'clientSecret' => 'zDy4aT6tmqYBgsg8WbU4GgZ8cwNwpM7g4F5BsmMP',
        'userName' => 'hercoinc@mileexpress.com.br',
        'password' => 'D29GdSPg3neSH8h',
        'scope' => '*'
    ],
    'tokenUrl' => '/oauth/token',
    'houseUrl' => '/v1/airwaybills/house',
    'trackingUrl' => '/v1/airwaybills/tracking?codes=',
    'createConsolidatorUrl' => '/v1/airwaybills/consolidators',
    'registerConsolidatorUrl' => '/v1/airwaybills/consolidators/attach',
    'createMasterUrl' => '/v1/airwaybills/master',
    'registerMasterUrl' => '/v1/airwaybills/master'
];