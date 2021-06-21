<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\ChileContainerRepository;
use App\Services\CorreosChile\ExportTxtChileManifestService;
use App\Services\CorreosChile\ExportExcelChileManifestService;
use App\Http\Requests\Warehouse\ChileContainer\CreateContainerRequest;
use App\Http\Requests\Warehouse\ChileContainer\UpdateContainerRequest;

class ChileContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ChileContainerRepository $chile_containerRepository)
    {
        $containers = $chile_containerRepository->get();
        
        return view('admin.warehouse.chileContainers.index', compact('containers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.warehouse.chileContainers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest  $createContainerRequest, ChileContainerRepository $chile_containerRepository)
    {
        if ( $container = $chile_containerRepository->store($createContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.chile_containers.index');
        }
        session()->flash('alert-danger', $chile_containerRepository->getError());
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

        if ( $container->isRegistered() ){
            abort(405);
        }

        return view('admin.warehouse.chileContainers.edit',compact('container'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $updateContainerRequest, Container $container, ChileContainerRepository $chile_containerRepository)
    {
        $container = Container::find($updateContainerRequest->id);

        if ( $container->isRegistered() ){
            abort(405);
        }
        
        if ( $container = $chile_containerRepository->update($container, $updateContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.chile_containers.index');
        }
        session()->flash('alert-danger', $chile_containerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($container, ChileContainerRepository $chile_containerRepository)
    {
        $container = Container::find($container);
        if ( $container->isRegistered() ){
            abort(403,'Cannot Delete Container registered on Correios.');
        }
        if ( $container = $chile_containerRepository->delete($container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.chile_containers.index');
        }

        session()->flash('alert-danger', $chile_containerRepository->getError());
        return back()->withInput();
    }

    public function download_txtManifest(Container $container)
    {
        $exportChileManifestService = new ExportTxtChileManifestService($container);
        return $exportChileManifestService->handle();
    }

    public function download_exceltManifest(Container $container)
    {
        $exportChileManifestService = new ExportExcelChileManifestService($container);
        return $exportChileManifestService->handle();
    }

}
