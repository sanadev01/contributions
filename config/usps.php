<?php

return [
    'production' => [
        'createLabelUrl' => 'https://api.myibservices.com/v1/labels',
        'deleteLabelUrl' => 'https://api.myibservices.com/v1/labels/',
        'createManifestUrl' => 'https://api.myibservices.com/v1/manifests.json',
        'getPriceUrl' => 'https://api.myibservices.com/v1/price.json',
        'trackingUrl' => 'https://api.myibservices.com/v1/track/',
        'addressValidationUrl' => 'https://api.myibservices.com/v1/address/validate',
        'email' => 'marcio.freitas@hercoinc.com',
        'password' => '150495Ca$',
    ],
    'testing' => [
        'createLabelUrl' => 'https://api-sandbox.myibservices.com/v1/labels',
        'deleteLabelUrl' => 'https://api-sandbox.myibservices.com/v1/labels/',
        'createManifestUrl' => 'https://api-sandbox.myibservices.com/v1/manifests.json',
        'getPriceUrl' => 'https://api-sandbox.myibservices.com/v1/price.json',
        'trackingUrl' => '',
        'addressValidationUrl' => 'https://api-sandbox.myibservices.com/v1/address/validate',
        'email' => 'mnaveedsaim@gmail.com',
        'password' => 'Ikonic@123',
    ],
    
];