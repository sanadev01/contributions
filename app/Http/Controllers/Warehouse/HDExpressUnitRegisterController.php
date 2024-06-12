<?php

namespace App\Http\Controllers\Warehouse;

use App\Facades\MileExpressFacade;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\ContainerRepository;

class HDExpressUnitRegisterController extends Controller
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
            
            if ($container->unit_code == null) {
                $container->update([
                    'unit_code' => 'HDC'.date('d').date('m').sprintf("%07d", $container->id).'BR',
                    'response' => true,
                ]);
                
                session()->flash('alert-success','Package Registration success. You can print Label now');
                return back();
            }
            else{
                
                session()->flash('alert-danger','Package Already Registered.');
                return back();
            }
        }

        session()->flash('alert-danger', 'Container does not have any packages/orders');
        return back();
    }
}
