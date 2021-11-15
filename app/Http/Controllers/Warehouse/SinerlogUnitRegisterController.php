<?php

namespace App\Http\Controllers\Warehouse;

use App\Services\Sinerlog\Client;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;

class SinerlogUnitRegisterController extends Controller
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
            $client = new Client();

            $response = $client->createBag($container);
            
            if($response->success == true)
            {
                //storing response in container table
                $container->update([
                    'unit_code' => $response->data->data->bag_id,
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
