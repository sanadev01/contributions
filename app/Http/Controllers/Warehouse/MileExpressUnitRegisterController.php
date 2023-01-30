<?php

namespace App\Http\Controllers\Warehouse;

use App\Facades\MileExpressFacade;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\ContainerRepository;

class MileExpressUnitRegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container, ContainerRepository $containerRepository)
    {
        if (count($container->orders) > 0){

            $airWayBillIds = $containerRepository->getAirWayBillIdsForMileExpress($container->orders);
            
            $containerConsolidatorData = json_decode($container->unit_response_list);
            
            $response = MileExpressFacade::registerContainer($containerConsolidatorData->id, $airWayBillIds);
            
            if ($response->success == false) {
                session()->flash('alert-danger', $response->message ?? 'error occured while registering container');
                return back();
            }

            if ($response->success == true) {
                $container->update([
                    'unit_code' => $containerConsolidatorData->code,
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
