<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\Sinerlog\Client;
use Illuminate\Support\Facades\Storage;

class SinerlogCN35DownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container)
    {
        if (!is_null($container->unit_code))
        {
            $client = new Client();
            $response = $client->getBagCN35($container);

            if ($response->success == true)
            {
                //storing response in container table
                $container->update([
                    'unit_response_list' => $response->data->data->file
                ]);

                session()->flash('alert-success','Label downloaded successfully. You can now print label');
                return back();
            }

            session()->flash('alert-danger',$response->message);
            return back();
        }

        session()->flash('alert-danger', 'Container is not registered yet');
        return back();
    }
}
