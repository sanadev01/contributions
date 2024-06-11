<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\GDEContainerRepository;
use App\Http\Requests\Warehouse\GDEContainer\CreateContainerRequest;
use App\Http\Requests\Warehouse\GDEContainer\UpdateContainerRequest;

class GDEContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GDEContainerRepository $gdeContainerRepository)
    {
        $containers = $gdeContainerRepository->get();
        
        return view('admin.warehouse.gdeContainers.index', compact('containers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.warehouse.gdeContainers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $createContainerRequest, GDEContainerRepository $gdeContainerRepository)
    {
        if ( $container = $gdeContainerRepository->store($createContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.gde_containers.index');
        }
        session()->flash('alert-danger', $gdeContainerRepository->getError());
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
        
        return view('admin.warehouse.gdeContainers.edit',compact('container'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $updateContainerRequest, GDEContainerRepository $gdeContainerRepository)
    {
        $container = Container::find($updateContainerRequest->id);

        if ( $container->response != 0 ){
            abort(405);
        }
        
        if ( $container = $gdeContainerRepository->update($container, $updateContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.gde_containers.index');
        }
        session()->flash('alert-danger', $gdeContainerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($container, GDEContainerRepository $gdeContainerRepository)
    {
        $container = Container::find($container);
        if ( $container->respone != 0 ){
            abort(403,'Cannot Delete Container registered on GDE');
        }
        if ( $container = $gdeContainerRepository->delete($container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.gde_containers.index');
        }

        session()->flash('alert-danger', $gdeContainerRepository->getError());
        return back()->withInput();        
    }

}
