<?php

return [
    'url' => env('CorreosChile_URL', 'https://apicert.correos.cl:8008/ServicioAdmisionCEPExterno/cch/ws/enviosCEP/externo/implementacion/ServicioExternoAdmisionCEP.asmx?WSDL'),
    'userId' => env('CorreosChile_userId', 'PRUEBA WS 1'),
    'correosKey' => env('CorreosChile_Key', 'b9d591ae8ef9d36bb7d4e18438d6114e'),
    'transactionId' => env('CorreosChile_transactionId', 'PRB20201103'),
    'codeId' => env('CorreosChile_codeId', '61001'),
];