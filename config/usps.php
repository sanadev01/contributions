<?php

return [
    'url' => env('USPS_URL', 'https://orderapi.myibservices.com/v1/labels'),
    'delete_label_url' => env('USPS_DELETE_LABEL_URL', 'https://orderapi.myibservices.com/v1/labels/'),
    'create_manifest_url' => env('USPS_CREATE_MANIFEST_URL', 'https://orderapi.myibservices.com/v1/manifests.json'),
    'get_price_url' => env('USPS_GET_PRICE_URL', 'https://orderapi.myibservices.com/v1/price.json'),
    'tracking_url' => env('USPS_TRACKING_URL', 'https://orderapi.myibservices.com/v1/track/'),
    'email' => env('USPS_Email', 'marcio.freitas@hercoinc.com'),
    'password' => env('USPS_Password', 'Marcio@1504'),
];