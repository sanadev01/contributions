<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\Excel\Export\ContainerOrderExport;
use App\Repositories\Warehouse\ContainerPackageRepository;

class ContainerPackageController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Container $container)
    {
        return view('admin.warehouse.containers.scan',compact('container'));
    }
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Container $container)
    {
        $orders = $container->orders;
        $exportService = new ContainerOrderExport($orders);
        return $exportService->handle();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Container $container, string $barcode, ContainerPackageRepository $containerPackageRepository)
    {
        $startTime = microtime(true); 
        $output = $containerPackageRepository->addOrderToContainer($container,$barcode);
         
        $endTime = microtime(true); 
        $executionTime = $endTime - $startTime;  
        \Log::info('Execution time of addOrderToContainer:' . $executionTime . ' seconds');
    
       return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Warehouse\Container  $container
     * @return \Illuminate\Http\Response
     */
    public function destroy(Container $container,Order $package, ContainerPackageRepository $containerPackageRepository)
    {
        try {
            //code...
            return $containerPackageRepository->removeOrderFromContainer($container,$package);
        } catch (\Exception $ex) {
            \Log::info($ex);
        }
    }
}
