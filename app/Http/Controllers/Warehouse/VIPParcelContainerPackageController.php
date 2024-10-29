<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\Excel\Export\ContainerOrderExport;
use App\Repositories\Warehouse\VIPParcelContainerPackageRepository;

class VIPParcelContainerPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $container = Container::find($id);
        $editMode = ($container->response == 0) ? true : false;

        return view('admin.warehouse.vipParcelContainers.scan',compact('container', 'editMode'));
                
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $container = Container::find($id);
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
    public function store($container, $order)
    {
        $vipParcelContainerPackageRepository = new VIPParcelContainerPackageRepository();

        return $vipParcelContainerPackageRepository->addOrderToContainer($container,$order);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($container, $id)
    {
        $vipParcelContainerPackageRepository = new VIPParcelContainerPackageRepository();
        try {
            //code...
            return $vipParcelContainerPackageRepository->removeOrderFromContainer($container,$id);
        } catch (\Exception $ex) {
            \Log::info($ex);
        }
    }

    public function uploadBulkTracking(Request $request, $id)
    {
        $vipParcelContainerPackageRepository = new VIPParcelContainerPackageRepository();

        return $vipParcelContainerPackageRepository->addTrackings($request, $id);
    }
}
