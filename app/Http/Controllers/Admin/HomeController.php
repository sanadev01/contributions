<?php

namespace App\Http\Controllers\Admin;

use Exception;
use SoapClient;
use App\Models\User;
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
        $direction = '1;calle tres 1302;la reina';


        try
        {
            $client = new Client();
            $response = $client->request('POST', $api_url, 
            [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                ],
                'form_params' => [
                    'usuario' => 'internacional',
                    'password' => 'QRxYTu#v',
                    'direccion' => trim($direction),
                ]
            ]);
            dd($response);
            
        }
        catch (Exception $e) {
            dd($e);
        }
    }

}
