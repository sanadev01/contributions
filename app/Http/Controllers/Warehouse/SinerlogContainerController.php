<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Repositories\Warehouse\SinerlogContainerRepository;
use App\Http\Requests\Warehouse\SinerlogContainer\CreateContainerRequest;
use App\Http\Requests\Warehouse\SinerlogContainer\UpdateContainerRequest;

class SinerlogContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SinerlogContainerRepository $sinerlog_containerRepository)
    {
        $containers = $sinerlog_containerRepository->get();

        return view('admin.warehouse.sinerlogContainers.index', compact('containers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.warehouse.sinerlogContainers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest  $createContainerRequest, SinerlogContainerRepository $sinerlog_containerRepository)
    {
        if ( $container = $sinerlog_containerRepository->store($createContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scan Packages');
            return redirect()->route('warehouse.sinerlog_containers.index');
        }
        session()->flash('alert-danger', $sinerlog_containerRepository->getError());
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
    public function edit(Container $sinerlog_container)
    {
        if ( $sinerlog_container->isRegistered() ){
            abort(405);
        }
        
        return view('admin.warehouse.sinerlogContainers.edit',compact('sinerlog_container'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $updateContainerRequest, Container $sinerlog_container, SinerlogContainerRepository $sinerlog_containerRepository)
    {
        if ( $sinerlog_container->isRegistered() ){
            abort(405);
        }
        
        if ( $sinerlog_container = $sinerlog_containerRepository->update($sinerlog_container, $updateContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.sinerlog_containers.index');
        }
        session()->flash('alert-danger', $sinerlog_containerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Container $sinerlog_container, SinerlogContainerRepository $sinerlog_containerRepository)
    {
        if ( $sinerlog_container->isRegistered() ){
            abort(403,'Cannot Delete Container registered on Correios.');
        }
        if ( $sinerlog_container = $sinerlog_containerRepository->delete($container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.containers.index');
        }

        session()->flash('alert-danger', $sinerlog_containerRepository->getError());
        return back()->withInput();
    }
}
