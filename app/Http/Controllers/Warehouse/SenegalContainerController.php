<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Repositories\Warehouse\SenegalContainerRepository;
use App\Http\Requests\Warehouse\SenegalContainer\CreateContainerRequest;
use App\Http\Requests\Warehouse\SenegalContainer\UpdateContainerRequest;

class SenegalContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SenegalContainerRepository $senegalContainerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        $containers = $senegalContainerRepository->get();

        return view('admin.warehouse.senegalContainers.index', compact('containers'));
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

        return view('admin.warehouse.senegalContainers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $request, SenegalContainerRepository $senegalContainerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        if ($senegalContainerRepository->store($request) ){
            session()->flash('alert-success', 'Container Saved Please Scan Packages');
            return redirect()->route('warehouse.hd-senegal-containers.index');
        }

        session()->flash('alert-danger', $senegalContainerRepository->getError());
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Container $hd_senegal_container)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($hd_senegal_container->isRegistered()) {
            return back();
        }
        
        return view('admin.warehouse.senegalContainers.edit')->with([
            'container' => $hd_senegal_container
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $request, Container $hd_senegal_container, SenegalContainerRepository $senegalContainerRepository)
    {
        if ($hd_senegal_container->isRegistered()) {
            return back();
        }
        if ( $senegalContainerRepository->update($hd_senegal_container, $request) ){
            session()->flash('alert-success', 'Container Saved Please Scan Packages');
            return redirect()->route('warehouse.hd-senegal-containers.index');
        }

        session()->flash('alert-danger', $senegalContainerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Container $hd_senegal_container, SenegalContainerRepository $senegalContainerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($hd_senegal_container->isRegistered()) {
            return back();
        }
        
        if ($senegalContainerRepository->delete($hd_senegal_container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.hd-senegal-containers.index');
        }

        session()->flash('alert-danger', $senegalContainerRepository->getError());
        return back();
    }
}
