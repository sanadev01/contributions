<?php

return [
    'url' => env('USPS_URL', 'https://api-sandbox.myibservices.com/v1/labels'),
    'delete_label_url' => env('USPS_DELETE_LABEL_URL', 'https://api-sandbox.myibservices.com/v1/labels/'),
    'create_manifest_url' => env('USPS_CREATE_MANIFEST_URL', 'https://api-sandbox.myibservices.com/v1/manifests.json'),
    'get_price_url' => env('USPS_GET_PRICE_URL', 'https://api-sandbox.myibservices.com/v1/price.json'),
    'email' => env('USPS_Email', 'ghaziislam3@gmail.com'),
    'password' => env('USPS_Password', 'Ikonic@1234'),
];