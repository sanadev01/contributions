<?php

return[
    'production' => [
        'baseUrl' => 'https://api.mileexpress.com.br',
    ],
    'testing' => [
        'baseUrl' => 'https://api.mileexpress.com.br',
    ],
    'credentials' => [
        'grant_type' => 'password',
        'clientId' => 1,
        'clientSecret' => 'xwShEAUn6MJ82AZzaECmypbp6PmjTM3HPhDYaxE7',
        'userName' => 'hercoinc@mileexpress.com.br',
        'password' => 'o867>P%0]^Rt',
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