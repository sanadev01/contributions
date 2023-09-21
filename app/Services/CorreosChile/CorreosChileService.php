<?php
namespace App\Services\CorreosChile;

use Exception;
use SoapClient;

class CorreosChileService
{
    protected $createShipmentUrl;
    protected $addressValidationUrl;
    protected $getRegionsUrl;
    protected $getCommunesUrl;
    protected $usuario;
    protected $contrasena;
    protected $codigoAdmision;
    protected $clienteRemitente;

    public function __construct($createShipmentUrl, $addressValidationUrl, $getRegionsUrl, $getCommunesUrl, $usuario, $contrasena, $codigoAdmision, $clienteRemitente)
    {
        $this->createShipmentUrl = $createShipmentUrl;
        $this->addressValidationUrl = $addressValidationUrl;
        $this->getRegionsUrl = $getRegionsUrl;
        $this->getCommunesUrl = $getCommunesUrl;
        $this->usuario = $usuario;
        $this->contrasena = $contrasena;
        $this->codigoAdmision = $codigoAdmision;
        $this->clienteRemitente = $clienteRemitente;
    }

    public function validateAddress($coummne, $address)
    {
       return $this->apiCallForAddressValidation($coummne, $address);
    }

    private function apiCallForAddressValidation($commune, $address)
    {
        $direction = '1;'.$address.';'.$commune;

        try
        {
            $options = array(
                'soap_version' => SOAP_1_1,
                'exceptions' => true,
                'trace' => 1,
                'connection_timeout' => 180,
                'cache_wsdl' => WSDL_CACHE_MEMORY,
            );

            $client = new SoapClient($this->addressValidationUrl, $options);
            $result = $client->__soapCall('Normalizar', array(
                'Normalizar' => array(
                    'usuario' => 'internacional',
                    'password' => 'QRxYTu#v',
                    'direccion' => trim($direction),
                )), null, null);
            return (Array)[
                        'success' => true,
                        'message' => 'Address Validated',
                        'data'    => $result->NormalizarResult,
                    ];
        }
        catch (Exception $e) {
            return (Array)[
                'success' => false,
                'message' => 'According to Correos Chile Your Address or House No is Inavalid',
            ];
        }
    }

    public function getAllRegions()
    {
        return $this->apiCallTogetAllRegions();
    }

    private function apiCallTogetAllRegions()
    {
        $client = new SoapClient($this->getRegionsUrl, array('trace' => 1, 'exception' => 0));
        try
        {
            $result = $client->__soapCall('listarTodasLasRegiones', array(
                'listarTodasLasRegiones' => array(
                    'usuario' => $this->usuario,
                    'contrasena' => $this->contrasena
                )), null, null);
                
            return (Array)[
                'success' => true,
                'message' => "Regions Fetched",
                'data'    => $result->listarTodasLasRegionesResult->RegionTO,
            ];
        } 
        catch (Exception $e) 
        {
            return (Array)[
                'success' => false,
                'message' => 'could not Load Regions plaease reload',
            ];
        }
    }

    public function getchileCommunes($regionCode)
    {
        return $this->apiCallTogetChileCommunesByRegion($regionCode);
    }

    private function apiCallTogetChileCommunesByRegion($regionCode)
    {
        try 
        {
            $client = new SoapClient($this->getCommunesUrl, array('trace' => 1, 'exception' => 0));
            $result = $client->__soapCall('listarComunasSegunRegion', array(
                'listarComunasSegunRegion' => array(
                    'usuario' => $this->usuario,
                    'contrasena' => $this->contrasena,
                    'codigoRegion' => $regionCode
            )), null, null);
            return (Array)[
                'success' => true,
                'message' => "Communes Fetched",
                'data'    => $result->listarComunasSegunRegionResult->ComunaTO,
            ];
        }
        catch (Exception $e) 
        {
            return (Array)[
                'success' => false,
                'message' => 'could not load Communes, please select region',
            ];
        }
    }

    public function generateLabel($order, $serviceType)
    {
        return $this->createShipment($this->makeRequestBodyForLabel($order, $serviceType));
    }

    private function makeRequestBodyForLabel($order, $serviceType)
    {
        
        $orderArray = [
            'CodigoAdmision' =>  $this->codigoAdmision,         //ID transaction (Internal Client), with this data the XML Response is returned.
            'ClienteRemitente' => $this->clienteRemitente,      //ID Code SAP Customer. Delivered by CorreosChile
            // 'CentroRemitente' => '',                            
            'NombreRemitente' => 'HERCO INC',                   //Sender Name
            'DireccionRemitente' => 'av. El parque 1307',      //Sender address
            'PaisRemitente' => '056',                           //Default “056” sender Country code(chile)    
            'CodigoPostalRemitente' => '9031244',                   //Sender Postal Code
            'ComunaRemitente' => 'Pudahuel',                    //Sender area/devision/city
            // 'RutRemitente' => '1-9',                            
            'PersonaContactoRemitente' => $order->sender_first_name,    //Sender Contact/person
            'TelefonoContactoRemitente' => $order->sender_phone,        //Sender Telephone
            // 'ClienteDestinatario' => '',                            //optional
            // 'CentroDestinatario' => '',                             //optional
            'NombreDestinatario' => $order->recipient->first_name.' '.$order->recipient->last_name, //Recipient Full Name
            'DireccionDestinatario' => $order->recipient->address,    //Recipient Address (street + number + complement address)
            'PaisDestinatario' => '056',                            //Destination Country (Default "056")
            'CodigoPostalDestinatario' => $order->recipient->zipcode, //Recipient Postal Code e.g 8340604
            'ComunaDestinatario' => $order->recipient->city,        //Recipient area/devision/city
            // 'RutDestinatario' => '',                                //optional
            'PersonaContactoDestinatario' => $order->recipient->first_name,    //Recipient Person
            'TelefonoContactoDestinatario' => $order->recipient->phone,        //Recipient Contact Number
            'CodigoServicio' => $serviceType,                                  //Service Type (SRP SERVICE CODE 28/SRM SERVICE CODE 32)
            'NumeroTotalPiezas' => 1,                                          //Number packages, the number associated shipping: 1
            'Kilos' => $order->weight,                                         //Weight: separated by POINT "." (Example 1.1).
            'Volumen' => $this->orderVolume($order->width, $order->length, $order->height),                                     //M3= (width x length x height) / 1000000 
            'NumeroReferencia' => $order->customer_reference,       //Reference number shipping Client
            'ImporteReembolso' => 0,                                //Refund amount
            'ImporteValorDeclarado' => $order->order_value,         //Order amount(the commercial value of the product shipped in whole numbers. For example: 50 USD)
            'TipoPortes' => 'P',                                    //Paid
            'Observaciones' => '',                                  //Observartion
            'Observaciones2' => '',                                 //Observartion
            'EmailDestino' => $order->recipient->email,              //Recipient Email
            'TipoMercancia' => $this->itemsDescription($order->items),                                   //Description that declares the content that travels in the package
            'DevolucionConforme' => 'N',                             //COMPLIANT RETURN (Default “N“)
            'NumeroDocumentos' => 0,                                 //Number of documents (Default 0)
            'PagoSeguro' => 'N',                                     //Insurance Payment (Default 'N')
        ];

        return $orderArray;
    }

    public function orderVolume($width, $length, $height)
    {
        $volume = ($width * $length * $height) / 1000000;
        return round($volume, 4);
    }

    public function itemsDescription($items)
    {
        foreach($items as $item)
        {
            $itemDescription[] = $item->description;
        }

        $description = implode(' ', $itemDescription);

        if (strlen($description) > 57)
        {
           return $description = str_limit($description, 54);
        }
        
        return $description;
    }

    private function createShipment($request_body)
    {
        try
        {
            $opts = array(
                'ssl' => array('ciphers' => 'RC4-SHA'),
                'http' => array(
                    'user_agent' => 'PHPSoapClient'
                )
            );
            $client = new SoapClient($this->createShipmentUrl, array('trace' => 1, 'exception' => 0, 'stream_context' => stream_context_create($opts)));
            $result = $client->__soapCall('admitirEnvio', array(
                'admitirEnvio' => array(
                    'usuario' => $this->usuario,
                    'contrasena' => $this->contrasena,
                    'admisionTo' => $request_body,
                )), null, null);
            return (Object)[
                    'success' => true,
                    'message' => "Label generated",
                    'data'    => $result->admitirEnvioResult,
            ];    
        }
        catch (Exception $e) {
            return (Object)[
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    

}
