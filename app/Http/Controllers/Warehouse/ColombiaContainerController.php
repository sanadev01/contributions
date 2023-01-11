<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\ContainerRepository;
use App\Http\Requests\Warehouse\Container\CreateContainerRequest;
use App\Http\Requests\Warehouse\Container\UpdateContainerRequest;

class ColombiaContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('admin.warehouse.colombiaContainer.index');
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

        return view('admin.warehouse.colombiaContainer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $request, ContainerRepository $containerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($containerRepository->store($request) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.colombia-containers.index');
        }

        session()->flash('alert-danger', $containerRepository->getError());
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Container $colombia_container)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ( $colombia_container->isRegistered() ){
            abort(405);
        }

        return view('admin.warehouse.colombiaContainer.edit')->with([
            'container' => $colombia_container
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $request, Container $colombia_container, ContainerRepository $containerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ( $colombia_container->isRegistered() ){
            abort(405);
        }

        if ( $containerRepository->update($colombia_container, $request) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.colombia-containers.index');
        }

        session()->flash('alert-danger', $containerRepository->getError());
        return back()->withInput();
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Container $colombia_container, ContainerRepository $containerRepository)
    {
        if ( $colombia_container->isRegistered() ){
            abort(405);
        }

        if ( $colombia_container->isRegistered() ){
            abort(403,'Cannot Delete registered Container');
        }

        if ($containerRepository->delete($colombia_container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.colombia-containers.index');
        }

        session()->flash('alert-danger', $containerRepository->getError());
        return back();
    }
}
