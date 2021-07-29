<?php

namespace App\Http\Controllers\Admin;

use Exception;
use SoapClient;
use App\Models\User;
use SimpleXMLElement;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        if ( !Session::has('last_logged_in') ){
            $user = Auth::user();
            if ($user->isUser() && $user->status == 'suspended') {
                Auth::logout();

                session()->flash('alert-danger','Your Account has been suspended Please contact Us / Sua conta foi suspensa Entre em contato conosco');
                return redirect()->route('login');
            }
        }

        return view('home');   
    }

    public function ChileAddress()
    {
        $api_url = 'http://cpinternacional.correos.cl:8008/ServEx.svc';
        $direction = '1;Estado 10;santiago';

        try
        {
            $options = array(
                'soap_version' => SOAP_1_1,
                'exceptions' => true,
                'trace' => 1,
                'connection_timeout' => 180,
                'cache_wsdl' => WSDL_CACHE_MEMORY,
            );

            $client = new SoapClient($api_url.'?wsdl', $options);
            $result = $client->__soapCall('Normalizar', array(
                'Normalizar' => array(
                    'usuario' => 'internacional',
                    'password' => 'QRxYTu#v',
                    'direccion' => trim($direction),
                )), null, null);
            dd($result);
        }
        catch (Exception $e) {
           dd($e);
        }
        // $body = [
        //     'internacional' => 'usuario',
        //     'QRxYTu#v' => 'password',
        //     trim($direction) => 'direccion',
        // ];

        // $xml = new SimpleXMLElement('<Normalizer/>');
        // array_walk_recursive($body, array ($xml, 'addChild'));
        // $xml_body = $xml->asXML();

        // try
        // {
        //     $client = new Client();
        //     $response = $client->request('POST', $api_url, 
        //     [
        //         'headers' => [
        //             'Content-Type' => 'text/xml; charset=utf-8',
        //         ],
        //         'body' => $xml_body,
        //     ]);
        //     dd($response);
            
        // }
        // catch (Exception $e) {
        //     dd($e);
        // }
    }

    public function testChile()
    {
        $api_url = 'http://cpinternacional.correos.cl:8008/ServEx.svc?WSDL';
        $direction = '1;calle tres 1302;la reina';
        try
        {
            $options = array(
                'soap_version' => SOAP_1_1,
                'exceptions' => true,
                'trace' => 1,
                'connection_timeout' => 180,
                'cache_wsdl' => WSDL_CACHE_MEMORY,
            );

            $client = new SoapClient($api_url, $options);
            $result = $client->__soapCall('Normalizar', array(
                'Normalizar' => array(
                    'usuario' => 'internacional',
                    'password' => 'QRxYTu#v',
                    'direccion' => trim($direction),
                )), null, null);
            return (Array)[
                'success' => true,
                'message' => 'Address Validated',
                'data'    => $result->Normalizar,
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
