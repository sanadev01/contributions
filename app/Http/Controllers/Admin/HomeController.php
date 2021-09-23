<?php

namespace App\Http\Controllers\Admin;


use stdClass;
use App\Models\User;
use App\Models\Order;
use App\Facades\USPSFacade;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Facades\CorreiosChileTrackingFacade;
use App\Facades\CorreiosBrazilTrackingFacade;

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

    public function test()
    {
        // $trackingNumber = '990077349283';
        // $response = CorreiosChileTrackingFacade::trackOrder($trackingNumber);

        // dd($response);

        $this->conversion();
    }

    public function conversion()
    {
        $data = [
            [
                'FechaDate' => '2020-05-30T04:40:00',
                'Fecha' => '30-05-2020 04:40',
                'Estado' => 'EN PREPARACION',
                'Oficina' => 'CORREOS CHILE',
                'Icono' => '011',
                'Orden' => 0,

            ],
            [
                'FechaDate' => '2020-05-30T04:40:00',
                'Fecha' => '30-05-2020 04:40',
                'Estado' => 'PENDIENTE DE ENTREGA. CONTACTAR SERVICIO AL CLIENTE CORREOSCHILE',
                'Oficina' => 'CORREOS CHILE',
                'Icono' => '007',
                'Orden' => 0,

            ]
        ];

        $response = (Object) [
            'status' => true,
            'message' => 'Order Found',
            'data' => $data,
        ];

        $order = Order::find(2761);

       $trackings = $this->pushToTrackings($response->data, $order->trackings);
        dd($trackings->toArray());
    }

    public function pushToTrackings($response, $hd_trackings)
    {
        foreach($response as $data)
        {
           
            $hd_trackings->push($data);
        }
       
        return $hd_trackings;
    }
    
    public function testBrazilTracking()
    {
        $api_url = config('usps.url');
        $delete_usps_label_url = config('usps.delete_label_url');
        $create_manifest_url = config('usps.create_manifest_url');
        $get_price_url = config('usps.get_price_url');
        $email = config('usps.email');           
        $password = config('usps.password');
        
        $trackingNumber = 'NX358146988BR';
        
        $response = CorreiosBrazilTrackingFacade::trackOrder($trackingNumber);

        dd($response);
    }
}
