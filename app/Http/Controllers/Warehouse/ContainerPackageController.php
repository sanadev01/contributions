<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Warehouse\Container;
use App\Repositories\Warehouse\ContainerPackageRepository;
use Illuminate\Http\Request;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Container $container, string $barcode, ContainerPackageRepository $containerPackageRepository)
    {
       return $containerPackageRepository->addOrderToContainer($container,$barcode);
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
