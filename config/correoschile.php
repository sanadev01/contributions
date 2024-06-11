<?php

return [
    'production' => [
        'createShipmentUrl' => env('CorreosChile_URL', 'http://apicert.correos.cl:8008/ServicioAdmisionCEPExterno/cch/ws/enviosCEP/externo/implementacion/ServicioExternoAdmisionCEP.asmx?WSDL'),
        'regions_url' => env('CorreosChile_Regions_URL', 'http://apicert.correos.cl:8008/ServicioRegionYComunasExterno/cch/ws/distribucionGeografica/externo/implementacion/ServicioExternoRegionYComunas.asmx?WSDL'),
        'communas_url' => env('CorreosChile_Communa_URL', 'http://apicert.correos.cl:8008/ServicioRegionYComunasExterno/cch/ws/distribucionGeografica/externo/implementacion/ServicioExternoRegionYComunas.asmx?WSDL'),
        'normalize_address_url' => env('CorreosChile_NormalizeAddress_URL', 'http://cpinternacional.correos.cl:8008/ServEx.svc?WSDL'),
        'userId' => env('CorreosChile_userId', 'PRUEBA WS 1'),
        'correosKey' => env('CorreosChile_Key', 'b9d591ae8ef9d36bb7d4e18438d6114e'),
        'transactionId' => env('CorreosChile_transactionId', 'PRB20201103'),
        'codeId' => env('CorreosChile_codeId', '61001'),
    ],
    'testing' => [
        'createShipmentUrl' => 'http://apicert.correos.cl:8008/ServicioAdmisionCEPExterno/cch/ws/enviosCEP/externo/implementacion/ServicioExternoAdmisionCEP.asmx?WSDL',
        'regions_url' => env('CorreosChile_Regions_URL', 'http://apicert.correos.cl:8008/ServicioRegionYComunasExterno/cch/ws/distribucionGeografica/externo/implementacion/ServicioExternoRegionYComunas.asmx?WSDL'),
        'communas_url' => env('CorreosChile_Communa_URL', 'http://apicert.correos.cl:8008/ServicioRegionYComunasExterno/cch/ws/distribucionGeografica/externo/implementacion/ServicioExternoRegionYComunas.asmx?WSDL'),
        'normalize_address_url' => env('CorreosChile_NormalizeAddress_URL', 'http://cpinternacional.correos.cl:8008/ServEx.svc?WSDL'),
        'userId' => 'PRUEBA WS 1',
        'correosKey' => 'b9d591ae8ef9d36bb7d4e18438d6114e',
        'transactionId' => 'PRB20201103',
        'codeId' => '61001',
    ]
];