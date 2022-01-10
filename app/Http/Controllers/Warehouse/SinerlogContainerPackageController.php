<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Services\Excel\Export\ContainerOrderExport;
use App\Repositories\Warehouse\SinerlogContainerPackageRepository;

class SinerlogContainerPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Container $sinerlog_container)
    {;
        return view('admin.warehouse.sinerlogContainers.scan',compact('sinerlog_container'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Container $sinerlog_container)
    {
        $orders = $sinerlog_container->orders;
        $exportService = new ContainerOrderExport($orders);
        return $exportService->handle();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Container $container, string $barcode, SinerlogContainerPackageRepository $sinerlogContainerPackageRepository)
    {
        return $sinerlogContainerPackageRepository->addOrderToContainer($container,$barcode);
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
    public function destroy(Container $sinerlog_container,Order $package, SinerlogContainerPackageRepository $sinerlogContainerPackageRepository)
    {
        try {
            //code...
            return $sinerlogContainerPackageRepository->removeOrderFromContainer($sinerlog_container,$package);
        } catch (\Exception $ex) {
            \Log::info($ex);
        }
    }
}
