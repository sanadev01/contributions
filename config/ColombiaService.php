<?php

return [
    'testing' => [
        'shippingUrl' => 'http://appcer.4-72.com.co/WcfServiceSPOKE/ServiceSPOKE.svc/PostShipping',
    ],
    'production' => [
        'shippingUrl' => 'http://appcer.4-72.com.co/WcfServiceSPOKE/ServiceSPOKE.svc/PostShipping',
    ],
    'credentials' => [
        'username' => env('COLOMBIA_USERNAME', 'herco.app'),
        'password' => env('COLOMBIA_PASSWORD', 'Colombia2021*'),
        'contractCode' => env('COLOMBIA_CONTRACT_CODE', '13348'),
        'headquarterCode' => env('COLOMBIA_HEADQUARTER_CODE', '76535'),
    ]
];