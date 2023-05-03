<?php

return [
    'testing' => [
        'shippingUrl' => 'http://appcer.4-72.com.co/WcfServiceSPOKE/ServiceSPOKE.svc/PostShippingWithoutFile',
        'containerRegisterUrl' => 'http://190.144.185.12:8290/servicio/pruebasEntrega/v1.0.0/pruebaEntrega',
    ],
    'production' => [
        'shippingUrl' => 'http://svc1.sipost.co/WcfServiceSPOKE/ServiceSPOKE.svc/PostShippingWithoutFile',
        'containerRegisterUrl' => 'http://190.144.185.12:8290/servicio/pruebasEntrega/v1.0.0/pruebaEntrega',
    ],
    'credentials' => [
        'username' => env('COLOMBIA_USERNAME', 'herco.app'),
        'password' => env('COLOMBIA_PASSWORD', 'Colombia2021*'),
        'contractCode' => env('COLOMBIA_CONTRACT_CODE', '13348'),
        'headquarterCode' => env('COLOMBIA_HEADQUARTER_CODE', '76535'),
        'token' => env('COLOMBIA_TOKEN', '403875e9e57440874702902c9e687dd9'),
    ]
];