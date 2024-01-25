<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Repositories\Warehouse\HDExpressContainerRepository;
use App\Http\Requests\Warehouse\HDExpressContainer\CreateContainerRequest;
use App\Http\Requests\Warehouse\HDExpressContainer\UpdateContainerRequest;

class HDExpressContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HDExpressContainerRepository $hdExpressContainerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        $containers = $hdExpressContainerRepository->get();

        return view('admin.warehouse.hdExpressContainers.index', compact('containers'));
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

        return view('admin.warehouse.hdExpressContainers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $request, HDExpressContainerRepository $hdExpressContainerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        if ($hdExpressContainerRepository->store($request)) {
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.hd-express-containers.index');
        }

        session()->flash('alert-danger', $hdExpressContainerRepository->getError());
        return back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Container $hd_express_container)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($hd_express_container->is_registered) {
            return back();
        }

        return view('admin.warehouse.hdExpressContainers.edit')->with([
            'container' => $hd_express_container
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $request, Container $hd_express_container, HDExpressContainerRepository $hdExpressContainerRepository)
    {
        if ($hd_express_container->is_registered) {
            return back();
        }
        if ($hdExpressContainerRepository->update($hd_express_container, $request)) {
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.hd-express-containers.index');
        }

        session()->flash('alert-danger', $hdExpressContainerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Container $hd_express_container, HDExpressContainerRepository $hdExpressContainerRepository)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($hd_express_container->is_registered) {
            return back();
        }

        if ($hdExpressContainerRepository->delete($hd_express_container)) {
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.hd-express-containers.index');
        }

        session()->flash('alert-danger', $hdExpressContainerRepository->getError());
        return back();
    }
}
