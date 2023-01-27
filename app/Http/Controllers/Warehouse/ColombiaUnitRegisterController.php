<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Facades\ColombiaShippingFacade;
use App\Services\Colombia\ColombiaService;
use Illuminate\Support\Facades\Log;

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
            
            $response = (new ColombiaService())->registerContainer($container);
            Log::info('colombia response',[$response]);
            if ($response['success'] == false) {
                session()->flash('alert-danger', $response['message'] ?? 'error occured while registering container');
                return back();
            }

            if ($response['success'] == true) {
                $date = date('YmdHis', strtotime(Carbon::now()));
                $code = "COHD".''.$date;
                $container->update([
                    'unit_response_list' => $response['data']['cargaPruebasEntregaRS']['identificadorTransaccion'],
                    'unit_code' => $code,
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