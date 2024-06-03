<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\PostNL\Client;

class POSTNLUnitRegisterController extends Controller
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
            $response = $client->createContainer($container);

            if ($response['success'] == false) {
                session()->flash('alert-danger', $response['message']);
                return back();
            }
            //storing response in container table
            $container->update([
                'unit_code' => $response['data']->data->assist_labels[0]->barcode,
                'unit_response_list' => json_encode($response['data']->data->assist_labels[0]),
                'response' => true,
            ]);

            session()->flash('alert-success','Package Registration success. You can print Label now');
            return back();
        }

        session()->flash('alert-danger', 'Container does not have any packages/orders');
        return back();
    }


}
