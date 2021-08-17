<?php

namespace App\Http\Controllers\Warehouse;

use App\Facades\USPSFacade;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;

class USPSUnitRegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container)
    {
        if (count($container->orders) > 0)
        {
            $response = USPSFacade::generateManifest($container);

            if($response->success == true)
            {
                //storing response in container table
                $container->update([
                    'unit_code' => $response->data['usps'][0]['manifest_number'],
                    'unit_response_list' => json_encode($response->data),
                    'response' => true,
                ]);

                session()->flash('alert-success','Package Registration success. You can print Label now');
                return back();
            }

            session()->flash('alert-danger',$response->message);
            return back();
        }

        session()->flash('alert-danger', 'Container does not have any packages/orders');
        return back();
    }
}
