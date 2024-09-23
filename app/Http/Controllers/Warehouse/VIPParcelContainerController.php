<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Repositories\Warehouse\VIPParcelContainerRepository;
use App\Http\Requests\Warehouse\VIPParcelContainer\CreateContainerRequest;
use App\Http\Requests\Warehouse\VIPParcelContainer\UpdateContainerRequest;

class VIPParcelContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VIPParcelContainerRepository $vipParcelContainerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        $containers = $vipParcelContainerRepository->get();

        return view('admin.warehouse.vipParcelContainers.index', compact('containers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('admin.warehouse.vipParcelContainers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $request, VIPParcelContainerRepository $vipParcelContainerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        if ($vipParcelContainerRepository->store($request) ){
            session()->flash('alert-success', 'Container Saved Please Scan Packages');
            return redirect()->route('warehouse.vip-parcel-containers.index');
        }

        session()->flash('alert-danger', $vipParcelContainerRepository->getError());
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Container $vip_parcel_container)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($vip_parcel_container->isRegistered()) {
            return back();
        }
        
        return view('admin.warehouse.vipParcelContainers.edit')->with([
            'container' => $vip_parcel_container
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $request, Container $vip_parcel_container, VIPParcelContainerRepository $vipParcelContainerRepository)
    {
        if ($vip_parcel_container->isRegistered()) {
            return back();
        }
        if ( $vipParcelContainerRepository->update($vip_parcel_container, $request) ){
            session()->flash('alert-success', 'Container Saved Please Scan Packages');
            return redirect()->route('warehouse.vip-parcel-containers.index');
        }

        session()->flash('alert-danger', $vipParcelContainerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Container $vip_parcel_container, VIPParcelContainerRepository $vipParcelContainerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($vip_parcel_container->isRegistered()) {
            return back();
        }
        
        if ($vipParcelContainerRepository->delete($vip_parcel_container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.vip-parcel-containers.index');
        }

        session()->flash('alert-danger', $vipParcelContainerRepository->getError());
        return back();
    }
}
