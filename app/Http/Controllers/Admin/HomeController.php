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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Facades\CorreiosChileTrackingFacade;
use App\Facades\CorreiosBrazilTrackingFacade;
use App\Facades\UPSFacade;

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
        $response = UPSFacade::getSenderPrice();
        if($response->success == true)
        {
            $data = $response->data;
           if($data['FreightRateResponse']['Response']['ResponseStatus']['Code'] == '1')
           {
               $rates = $data['FreightRateResponse']['Rate'];
               foreach($rates as $rate)
               {
                   if($rate['Type']['Code'] == 'LND_GROSS')
                   {
                       $this->rate = $rate['Factor']['Value'];
                   }
               }
           }
        }
        dd($response);
        // if($response->successful())
        // {
        //     // if($response['FreightRateResponse']['Response']['ResponseStatus']['Code'] == '1')
        //     // {
        //     //     dd(true);
        //     // }
        //     dd($response['FreightRateResponse']);
        // }
        dd($response['FreightRateResponse']['Response']['ResponseStatus']['Code']);
        $request_body = (Object)[
            'FreightRateRequest' => (Object)[
                'ShipFrom' => (Object)[
                    'Name' => 'Ghazi',
                    'Address' => (Object)[
                        'AddressLine' => '123 Lane',
                        'City' => 'TIMONIUM',
                        'StateProvinceCode' => 'MD',
                        'PostalCode' => '21093',
                        'CountryCode' => 'US',
                    ],
                    'AttentionName' => 'Ghazi',
                    'Phone' => (Object)[
                        'Number' => '4444444444',
                        'Extension' => '4444',
                    ],
                    'EMailAddress' => 'homedelivery@homedeliverybr.com'
                ],
                'ShipperNumber' => 'AT0123',
                'ShipTo' => (Object)[
                    'Name' => 'HERCO SUIT#100',
                    'Address' => (Object)[
                        'AddressLine' => '2200 NW 129TH AVE',
                        'City' => 'Miami',
                        'StateProvinceCode' => 'FL',
                        'PostalCode' => '33182',
                        'CountryCode' => 'US',
                    ],
                    'AttentionName' => 'Marcio',
                    'Phone' => (Object)[
                        'Number' => '4444444444',
                        'Extension' => '4444',
                    ],
                    'EMailAddress' => 'homedelivery@homedeliverybr.com'
                ],
                'PaymentInformation' => (Object)[
                    'Payer' => (Object)[
                        'Name' => 'HERCO SUIT#100',
                        'Address' => (Object)[
                            'AddressLine' => '2200 NW 129TH AVE',
                            'City' => 'Miami',
                            'StateProvinceCode' => 'FL',
                            'PostalCode' => '33182',
                            'CountryCode' => 'US',
                        ],
                        'ShipperNumber' => 'AT0123',
                        'AccountType' => '1',
                        'AttentionName' => 'Marcio',
                        'Phone' => (Object)[
                            'Number' => '4444444444',
                            'Extension' => '4444',
                        ],
                        'EMailAddress' => 'homedelivery@homedeliverybr.com'
                    ],
                    'ShipmentBillingOption' => (Object)[
                        'Code' => '10',
                    ],
                ],
                'Service' =>  (Object)[
                    'Code' => '308',
                ],
                'Commodity' => (Object)[
                    'Description' => 'FRS-Freight',
                    'Weight' => (Object)[
                        'UnitOfMeasurement' => (Object)[
                            'Code' => 'LBS'
                        ],
                        'Value' => '150',
                    ],
                    'Dimensions' => (Object)[
                        'UnitOfMeasurement' => (Object)[
                            'Code' => 'IN',
                            'Description' => ''
                        ],
                        'Length' => '9',
                        'Width' => '5',
                        'Height' => '4',
                    ],
                    'NumberOfPieces' => (Object)[
                        'PackagingType' => (Object)[
                            'Code' => 'BOX',
                        ],
                        'FreightClass' => '60',
                    ]

                ],
                'DensityEligibleIndicator' => '',
                // 'AlternateRateOptions' => [
                //     'Code' => '1',
                // ],
            ],
        ];

        dd($request_body);
        $rates = [
            (Object)[
                'type' => (Object)[
                    'code' => 2,
                    'Description' => 2,
                ],
                'factor' => (Object)[
                    'value' => '39.02',
                    'UnitOfMeasurement' => (Object)[
                        'code' => 'USD'
                    ]
                ],
            ],
            (Object)[
                'type' => (Object)[
                    'code' => 'LND_GROSS',
                    'Description' => 'LND_GROSS',
                ],
                'factor' => (Object)[
                    'value' => '144.50',
                    'UnitOfMeasurement' => (Object)[
                        'code' => 'USD'
                    ]
                ],
            ],
        ];

        
        foreach($rates as $rate)
        {
            if($rate->type->code == 'LND_GROSS')
            {
                var_dump($rate->factor->value);
            }
            // var_dump($rate->type->code);
        }
        dd($rates);
        // try {
            
        //     $response = Http::withBasicAuth('herco.app', 'Colombia2021*')->get('http://appcer.4-72.com.co/WcfServiceSPOKE/ServiceSPOKE.svc/GetHeadquarter/0');
        //     dd($response->json());
        // } catch (\Exception $e) {
        //     dd($e->getMessage());
        // }
        // dd(true);
        $wsdlUrl = config('correoschile.url');
        $usuario = config('correoschile.userId');              //CorreosChile user Id
        $contrasena = config('correoschile.correosKey');  //CorreosChile Key
        $codigoAdmision = config('correoschile.transactionId');   // ID transaction (Internal Client), with this data the XML Response is returned.
        $clienteRemitente = config('correoschile.codeId');  
        
        dd($wsdlUrl, $usuario, $contrasena, $codigoAdmision, $clienteRemitente);
        
        $trackingNumber = 'NX358146988BR';
        
        $response = CorreiosBrazilTrackingFacade::trackOrder($trackingNumber);

        dd($response);
    }
}
