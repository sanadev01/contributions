<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\DirectLinkContainerRepository;
use App\Http\Requests\Warehouse\DirectLinkContainer\CreateContainerRequest;
use App\Http\Requests\Warehouse\DirectLinkContainer\UpdateContainerRequest;

class DirectLinkContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DirectLinkContainerRepository $directlink_containerRepository)
    {
        $containers = $directlink_containerRepository->get();
        
        return view('admin.warehouse.directlinkContainers.index', compact('containers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.warehouse.directlinkContainers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $createContainerRequest, DirectLinkContainerRepository $directlink_containerRepository)
    {
        if ( $container = $directlink_containerRepository->store($createContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.directlink_containers.index');
        }
        session()->flash('alert-danger', $directlink_containerRepository->getError());
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
        
        return view('admin.warehouse.directlinkContainers.edit',compact('container'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $updateContainerRequest, DirectLinkContainerRepository $directlink_containerRepository)
    {
        $container = Container::find($updateContainerRequest->id);

        if ( $container->response != 0 ){
            abort(405);
        }
        
        if ( $container = $directlink_containerRepository->update($container, $updateContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.directlink_containers.index');
        }
        session()->flash('alert-danger', $directlink_containerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($container, DirectLinkContainerRepository $directlink_containerRepository)
    {
        $container = Container::find($container);
        if ( $container->respone != 0 ){
            abort(403,'Cannot Delete Container registered on Correios Chile.');
        }
        if ( $container = $directlink_containerRepository->delete($container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.directlink_containers.index');
        }

        session()->flash('alert-danger', $directlink_containerRepository->getError());
        return back()->withInput();        
    }

}
