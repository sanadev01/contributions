<?php
   $testBaseUrl = 'https://hmlapi.nobordist.com';
   $productionBaseUrl = 'https://hmlapi-total.nobordist.com';
  
return [
  'test' => [ 
        "email"=>  "marcio.freitas@hercoinc.com",
        "password"=> "91227898010937YYDHg!", 
        'baseUrl' => $testBaseUrl,
        'baseAuthUrl' => "$testBaseUrl/authenticate/total/seller",
        'contractId' => '109',
    ],
    'production' => [
        'email' => 'marcio.freitas@hercoinc.com',
        'password' => 'Hercomv1504!',
        'baseUrl' => $productionBaseUrl,
        'baseAuthUrl' => "$productionBaseUrl/authenticate/total/seller",
        'contractId' => '129',
        ],
    ];