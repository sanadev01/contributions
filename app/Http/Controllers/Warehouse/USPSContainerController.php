<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\USPS\ExportExcelUSPSManifestService;
use App\Repositories\Warehouse\USPSContainerRepository;
use App\Http\Requests\Warehouse\UspsContainer\CreateContainerRequest;
use App\Http\Requests\Warehouse\UspsContainer\UpdateContainerRequest;

class USPSContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(USPSContainerRepository $usps_containerRepository)
    {
        $containers = $usps_containerRepository->get();
        
        return view('admin.warehouse.uspsContainers.index', compact('containers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.warehouse.uspsContainers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $createContainerRequest, USPSContainerRepository $usps_containerRepository)
    {
        if ( $container = $usps_containerRepository->store($createContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.usps_containers.index');
        }
        session()->flash('alert-danger', $usps_containerRepository->getError());
        return back()->withInput();
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
    public function edit($container)
    {
        $container = Container::find($container);

        if ( $container->response != 0 ){
            abort(405);
        }
        
        return view('admin.warehouse.uspsContainers.edit',compact('container'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $updateContainerRequest, USPSContainerRepository $usps_containerRepository)
    {
        $container = Container::find($updateContainerRequest->id);

        if ( $container->response != 0 ){
            abort(405);
        }
        
        if ( $container = $usps_containerRepository->update($container, $updateContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.usps_containers.index');
        }
        session()->flash('alert-danger', $usps_containerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($container, USPSContainerRepository $usps_containerRepository)
    {
        $container = Container::find($container);
        if ( $container->respone != 0 ){
            abort(403,'Cannot Delete Container registered on Correios Chile.');
        }
        if ( $container = $usps_containerRepository->delete($container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.usps_containers.index');
        }

        session()->flash('alert-danger', $usps_containerRepository->getError());
        return back()->withInput();        
    }

    public function download_exceltManifest(Container $container)
    {
        $exportChileManifestService = new ExportExcelUSPSManifestService($container);
        return $exportChileManifestService->handle();
    }
}
