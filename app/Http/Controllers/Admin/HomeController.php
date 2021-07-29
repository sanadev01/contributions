<?php

namespace App\Http\Controllers\Admin;

use Exception;
use SoapClient;
use App\Models\User;
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
        $direction = '1;calle tres 1302;la reina';

        
        try
        {
            $opts = array(
                    'http' => array(
                        'user_agent' => 'PHPSoapClient'
                    ),
                    'ssl' => array(
                        'ciphers'     => 'RC4-SHA',
                        'verify_peer' => false, 
                        'verify_peer_name' => false 
                    ),
            );
            $context = stream_context_create($opts);
            $soapClientOptions = array(
                        'stream_context' => $context,
                        'encoding'           => 'UTF-8',
                        'verifypeer'         => false,
                        'verifyhost'         => false,
                        'soap_version'       => SOAP_1_2,
                        'trace'              => 1,
                        'exceptions'         => 1,
                        'connection_timeout' => 180,
                        );

            $client = new SoapClient($api_url, $soapClientOptions);
            $result = $client->__soapCall('Normalizar', array(
                'Normalizar' => array(
                    'usuario' => 'internacional',
                    'password' => 'QRxYTu#v',
                    'direccion' => trim($direction),
                )), null, null);
            dd($result->Normalizar, $result);
        }
        catch (Exception $e) {
            dd($e);
        }
    }

}
