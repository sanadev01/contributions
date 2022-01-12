<?php

return [
    'production' => [
        'createLabelUrl' => env('USPS_URL', 'https://api.myibservices.com/v1/labels'),
        'deleteLabelUrl' => env('USPS_DELETE_LABEL_URL', 'https://api.myibservices.com/v1/labels/'),
        'createManifestUrl' => env('USPS_CREATE_MANIFEST_URL', 'https://api.myibservices.com/v1/manifests.json'),
        'getPriceUrl' => env('USPS_GET_PRICE_URL', 'https://api.myibservices.com/v1/price.json'),
        'trackingUrl' => env('USPS_TRACKING_URL', 'https://api.myibservices.com/v1/track/'),
        'addressValidationUrl' => 'https://api.myibservices.com/v1/address/validate',
        'email' => env('USPS_Email', 'marcio.freitas@hercoinc.com'),
        'password' => env('USPS_Password', 'Marcio@1504'),
    ],
    'testing' => [
        'createLabelUrl' => 'https://api-sandbox.myibservices.com/v1/labels',
        'deleteLabelUrl' => 'https://api-sandbox.myibservices.com/v1/labels/',
        'createManifestUrl' => 'https://api-sandbox.myibservices.com/v1/manifests.json',
        'getPriceUrl' => 'https://api-sandbox.myibservices.com/v1/price.json',
        'trackingUrl' => '',
        'addressValidationUrl' => 'https://api-sandbox.myibservices.com/v1/address/validate',
        'email' => 'ghaziislam3@gmail.com',
        'password' => 'Ikonic@1234',
    ],
    
];