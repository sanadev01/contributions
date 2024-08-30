<?php

return [
    'production' => [
        'createShipmentUrl' => env('CorreosChile_URL', 'http://apicert.correos.cl:8008/ServicioAdmisionCEPExterno/cch/ws/enviosCEP/externo/implementacion/ServicioExternoAdmisionCEP.asmx?WSDL'),
        'regions_url' => env('CorreosChile_Regions_URL', 'http://apicert.correos.cl:8008/ServicioRegionYComunasExterno/cch/ws/distribucionGeografica/externo/implementacion/ServicioExternoRegionYComunas.asmx?WSDL'),
        'communas_url' => env('CorreosChile_Communa_URL', 'http://apicert.correos.cl:8008/ServicioRegionYComunasExterno/cch/ws/distribucionGeografica/externo/implementacion/ServicioExternoRegionYComunas.asmx?WSDL'),
        'normalize_address_url' =>'http://cpinternacional.correos.cl:8008/ServEx.svc?WSDL',
        'userId' => env('prodCorreosChile_userId'),
        'correosKey' => env('prodCorreosChile_Key'),
        'transactionId' => env('prodCorreosChile_transactionId'),
        'codeId' => env('prodCorreosChile_codeId'),
    ],
    'testing' => [
        'createShipmentUrl' => 'http://apicert.correos.cl:8008/ServicioAdmisionCEPExterno/cch/ws/enviosCEP/externo/implementacion/ServicioExternoAdmisionCEP.asmx?WSDL',
        'regions_url' => env('CorreosChile_Regions_URL', 'http://apicert.correos.cl:8008/ServicioRegionYComunasExterno/cch/ws/distribucionGeografica/externo/implementacion/ServicioExternoRegionYComunas.asmx?WSDL'),
        'communas_url' => env('CorreosChile_Communa_URL', 'http://apicert.correos.cl:8008/ServicioRegionYComunasExterno/cch/ws/distribucionGeografica/externo/implementacion/ServicioExternoRegionYComunas.asmx?WSDL'),
        'normalize_address_url' =>'http://cpinternacional.correos.cl:8008/ServEx.svc?WSDL',
        'userId' => env('testCorreosChile_UserID'),
        'correosKey' => env('testCorreosChile_Key'),
        'transactionId' => env('testCorreosChile_transactionId'),
        'codeId' => env('testCorreosChile_codeId'),
    ]
];