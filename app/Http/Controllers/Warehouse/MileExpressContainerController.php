<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\MileExpressContainerRepository;
use App\Http\Requests\Warehouse\Container\CreateContainerRequest;
use App\Http\Requests\Warehouse\Container\UpdateContainerRequest;
use App\Models\Warehouse\Container;

class MileExpressContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(MileExpressContainerRepository $mileExpressContainerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $containers = $mileExpressContainerRepository->get();
        return view('admin.warehouse.mileExpressContainer.index', compact('containers'));
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

        return view('admin.warehouse.mileExpressContainer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $request, MileExpressContainerRepository $containerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        if ($containerRepository->store($request) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.mile_express_containers.index');
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
    public function edit(Container $mile_express_container)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($mile_express_container->isRegistered()) {
            return back();
        }
        
        return view('admin.warehouse.mileExpressContainer.edit')->with([
            'container' => $mile_express_container
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $request, Container $mile_express_container, MileExpressContainerRepository $containerRepository)
    {
        if ($mile_express_container->isRegistered()) {
            return back();
        }
        if ( $containerRepository->update($mile_express_container, $request) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.mile_express_containers.index');
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
    public function destroy(Container $mile_express_container, MileExpressContainerRepository $containerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($mile_express_container->isRegistered()) {
            return back();
        }
        
        if ($containerRepository->delete($mile_express_container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.mile_express_containers.index');
        }

        session()->flash('alert-danger', $containerRepository->getError());
        return back();
    }
}
