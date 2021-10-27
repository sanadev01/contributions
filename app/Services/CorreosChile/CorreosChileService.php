<?php
namespace App\Services\CorreosChile;

use Exception;
use SoapClient;

class CorreosChileService
{
    protected $wsdlUrl;
    protected $usuario;
    protected $contrasena;
    protected $codigoAdmision;
    protected $clienteRemitente;

    public function __construct($wsdlUrl, $usuario, $contrasena, $codigoAdmision, $clienteRemitente)
    {
        $this->wsdlUrl = $wsdlUrl;
        $this->usuario = $usuario;
        $this->contrasena = $contrasena;
        $this->codigoAdmision = $codigoAdmision;
        $this->clienteRemitente = $clienteRemitente;
    }

    public function generateLabel($order, $serviceType)
    {
        $request_body = $this->make_body_attributes($order, $serviceType);
        
        $correos_chile_api_response = $this->AdmitShipment($request_body);
        
        return $correos_chile_api_response;
    }

    public function make_body_attributes($order, $serviceType)
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

    function AdmitShipment($request_body)
    {
        try
        {
            $opts = array(
                'ssl' => array('ciphers' => 'RC4-SHA'),
                'http' => array(
                    'user_agent' => 'PHPSoapClient'
                )
            );
            $client = new SoapClient($this->wsdlUrl, array('trace' => 1, 'exception' => 0, 'stream_context' => stream_context_create($opts)));
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
