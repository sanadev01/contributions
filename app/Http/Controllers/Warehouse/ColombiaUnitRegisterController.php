<?php

namespace App\Http\Controllers\Warehouse;

use App\Facades\ColombiaShippingFacade;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;

class ColombiaUnitRegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container)
    {
        if (count($container->orders) > 0){

            $response = ColombiaShippingFacade::registerContainer($container);

            if ($response['success'] == false) {
                session()->flash('alert-danger', $response['message'] ?? 'error occured while registering container');
                return back();
            }

            if ($response['success'] == true) {

                $container->update([
                    'unit_code' => $response['data']['cargaPruebasEntregaRS']['identificadorTransaccion'],
                    'response' => true,
                ]);

                session()->flash('alert-success','Package Registration success. You can print Label now');
                return back();
            }
        }

        session()->flash('alert-danger', 'Container does not have any packages/orders');
        return back();
    }
}