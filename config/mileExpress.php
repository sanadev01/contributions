<?php

return[
    'production' => [
        'baseUrl' => '',
    ],
    'testing' => [
        'baseUrl' => 'https://dev.mileexpress.com.br',
    ],
    'credentials' => [
        'clientId' => 3,
        'clientSecret' => 'ydwLy3QzUbA90Yp5vTI3CuvTbSH3rvwlZDLKMYDa',
        'userName' => 'hercoinc@mileexpress.com.br',
        'password' => 'D29GdSPg3neSH8h',
    ],
    'tokenUrl' => '/oauth/token',
    'houseUrl' => '/v1/airwaybills/house',
    'trackingUrl' => '/v1/airwaybills/tracking?codes=',
    'createConsolidatorUrl' => '/v1/airwaybills/consolidators',
    'registerConsolidatorUrl' => '/v1/airwaybills/consolidators/attach',
    'createMasterUrl' => '/v1/airwaybills/master',
    'registerMasterUrl' => '/v1/airwaybills/master'
];