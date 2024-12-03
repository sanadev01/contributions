<?php
return [
    'production' => [
        'customsBaseUri'  => env('PROD_CUSTOMS_BASE_URI'),
        'clientId'        => env('PROD_CLIENT_ID'),
        'clientSecret'    => env('PROD_CLIENT_SECRET'),
        'webhookUrl'      => env('PROD_WEBHOOK_URL'),
    ],
    'testing' => [
        'customsBaseUri'  => env('TEST_CUSTOMS_BASE_URI'),
        'clientId'        => env('TEST_CLIENT_ID'),
        'clientSecret'    => env('TEST_CLIENT_SECRET'),
        'webhookUrl'      => env('TEST_WEBHOOK_URL'),
    ],
];
