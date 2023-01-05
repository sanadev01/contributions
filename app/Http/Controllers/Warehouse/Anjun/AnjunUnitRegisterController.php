<?php

namespace App\Http\Controllers\Warehouse\Anjun;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\Anjun\AnjunClient;

class AnjunUnitRegisterController extends Controller
{
    public function __invoke(Container $container)
    {
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger', 'Please add parcels to this container');
            return back();
        }
        $response = (new AnjunClient())->createContainer($container);
        $data = $response->getData();

        if ($data->success) {
            $container->update([
                'unit_response_list' => json_encode($data->output),
                'unit_code' => $data->output->barCode
            ]);
        } else {
            session()->flash('alert-danger', $data->message);
            return back();
        }
        session()->flash('alert-success', 'Package Registration success. You can print Label now');
        return back();
    }
}
