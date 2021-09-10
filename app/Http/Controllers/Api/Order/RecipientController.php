<?php

namespace App\Http\Controllers\Api\Order;

use Exception;
use SoapClient;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use FlyingLuscas\Correios\Client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class RecipientController extends Controller
{
    private $usuario;
    private $contrasena;


    public function __construct()
    {
        $this->usuario = config('correoschile.userId');
        $this->contrasena = config('correoschile.correosKey');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $address = Address::find($request->address_id);
        if ( !$address ){
            return apiResponse(false,"Address Not found");
        }

        $order = Order::find($request->order_id);

        $order->update([
            'recipient_address_id' => $address->id
        ]);
        
        if ( $order->recipient ){

            $order->recipient()->update([
                'first_name' => $address->first_name,
                'last_name' => $address->last_name,
                'email' => $address->email,
                'phone' => $address->phone,
                'city' => $address->city,
                'street_no' => $address->street_no,
                'address' => $address->address,
                'address2' => $address->address2,
                'account_type' => $address->account_type,
                'tax_id' => $address->tax_id,
                'zipcode' => $address->zipcode,
                'state_id' => $address->state_id,
                'country_id' => $address->country_id,
            ]);

            return apiResponse(true,'Address Updated');

        }

        $order->recipient()->create([
            'first_name' => $address->first_name,
            'last_name' => $address->last_name,
            'email' => $address->email,
            'phone' => $address->phone,
            'city' => $address->city,
            'street_no' => $address->street_no,
            'address' => $address->address,
            'address2' => $address->address2,
            'account_type' => $address->account_type,
            'tax_id' => $address->tax_id,
            'zipcode' => $address->zipcode,
            'state_id' => $address->state_id,
            'country_id' => $address->country_id,
        ]);

        return apiResponse(true,'Address Updated');
    }

    public function zipcode(Request $request)
    {
        $correios = new Client;
        $response = $correios->zipcode()->find($request->zipcode);
        
        if(optional($response)['error']){
            return apiResponse(false,'zip code not found / CEP não encontrado');
        }
        return apiResponse(true,'Zipcode success',$response);
    }

    public function chileRegions()
    {
        return $regions = $this->getAllRegions();
        
    }

    public function chileCommunes(Request $request)
    {

        return $communes = $this->getCommunesByRegion($request->region_code);
    }

    public function normalizeAddress(Request $request)
    {
       return $this->validateChileAddress($request->coummne, $request->address);

    }

    function getAllRegions() 
    {
        $wsdlUrl = config('correoschile.regions_url');

        $client = new SoapClient($wsdlUrl, array('trace' => 1, 'exception' => 0));
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

    public function getCommunesByRegion($region_code)
    {
        $wsdlUrl = config('correoschile.communas_url');
        
        try 
        {
            $client = new SoapClient($wsdlUrl, array('trace' => 1, 'exception' => 0));
            $result = $client->__soapCall('listarComunasSegunRegion', array(
                'listarComunasSegunRegion' => array(
                    'usuario' => $this->usuario,
                    'contrasena' => $this->contrasena,
                    'codigoRegion' => $region_code
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

    function validateChileAddress($commune, $address)
    {
        $wsdlUrl = config('correoschile.normalize_address_url');
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

            $client = new SoapClient($wsdlUrl, $options);
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

    public function validate_USAddress(Request $request)
    {
        $api_url = 'https://api-sandbox.myibservices.com/v1/address/validate';
        $email = config('usps.email');           
        $password = config('usps.password');

        $data = $this->make_request_attributes($request->state, $request->city, $request->address);

        try {

            $response = Http::withBasicAuth($email, $password)->post($api_url, $data);
            // dd($response->status(),$response->json());
            if($response->status() == 200) {
                
                return (Array)[
                    'success' => true,
                    'zipcode'    => $response->json()['zip5'],
                ];
            }

            if($response->status() != 200) {
                return (Array)[
                    'success' => false,
                    'message' => $response->json()['message'],
                ];
            }
        } catch (Exception $e) {
            
            return (Array)[
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

    }

    public function make_request_attributes($state,$city,$address)
    {
        $data = [
            'company_name' => 'Herco',
            'line1' => $address,
            'state_province' => $state,
            'city' => $city,
            'postal_code' => '',
            'country_code' => 'US'
        ];

        return $data;
    }
}
