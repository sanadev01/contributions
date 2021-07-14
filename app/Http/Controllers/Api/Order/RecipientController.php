<?php

namespace App\Http\Controllers\Api\Order;

use Exception;
use SoapClient;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use FlyingLuscas\Correios\Client;
use App\Http\Controllers\Controller;

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
       return $this->validateChileAddress($request->coummne, $request->direction);

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

    function validateChileAddress($commune, $direction)
    {
        $wsdlUrl = config('correoschile.normalize_address_url');
        $id = '.';
        
        try
        {
            $client = new SoapClient($wsdlUrl, array('trace' => 1, 'exception' => 0));
            $result = $client->__soapCall('normalizarDireccion', array(
                'normalizarDireccion' => array(
                    'usuario' => $this->usuario,
                    'contrasena' => $this->contrasena,
                    'id' => $id,
                    'direccion' => trim($direction),
                    'comuna' => trim($commune)
                )), null, null);
            return (Array)[
                'success' => true,
                'message' => 'Address Validated',
                'data'    => $result->normalizarDireccionResult,
            ];
        }
        catch (Exception $e) {
            return (Array)[
                'success' => false,
                'message' => 'According to Correos Chile Your Address or House No is Inavalid',
            ];
        }
    }

}
