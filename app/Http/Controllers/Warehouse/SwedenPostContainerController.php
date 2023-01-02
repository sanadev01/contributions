<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\SwedenPostContainerRepository;
use App\Http\Requests\Warehouse\SwedenPostContainer\CreateContainerRequest;
use App\Http\Requests\Warehouse\SwedenPostContainer\UpdateContainerRequest;

class SwedenPostContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SwedenPostContainerRepository $swedenpostContainerRepository)
    {
        $containers = $swedenpostContainerRepository->get();
        
        return view('admin.warehouse.swedenpostContainers.index', compact('containers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.warehouse.swedenpostContainers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $createContainerRequest, SwedenPostContainerRepository $swedenpostContainerRepository)
    {
        if ( $container = $swedenpostContainerRepository->store($createContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.swedenpost_containers.index');
        }
        session()->flash('alert-danger', $swedenpostContainerRepository->getError());
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
        return view('admin.warehouse.swedenpostContainers.edit',compact('container'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $updateContainerRequest, SwedenPostContainerRepository $swedenpostContainerRepository)
    {
        $container = Container::find($updateContainerRequest->id);

        if ( $container->response != 0 ){
            abort(405);
        }
        if ( $container = $swedenpostContainerRepository->update($container, $updateContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.swedenpost_containers.index');
        }
        session()->flash('alert-danger', $swedenpostContainerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($container, SwedenPostContainerRepository $swedenpostContainerRepository)
    {
        $container = Container::find($container);
        if ( $container->respone != 0 ){
            abort(403,'Cannot Delete Container registered on Correios Chile.');
        }
        if ( $container = $swedenpostContainerRepository->delete($container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.swedenpost_containers.index');
        }

        session()->flash('alert-danger', $swedenpostContainerRepository->getError());
        return back()->withInput();        
    }

}
