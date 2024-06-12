<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\HoundContainerRepository;
use App\Http\Requests\Warehouse\Container\CreateContainerRequest;
class HoundContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HoundContainerRepository $houndContainerRepository)
    {
        $containers = $houndContainerRepository->get();        
        return view('admin.warehouse.houndContainers.index', compact('containers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.warehouse.houndContainers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $createContainerRequest, HoundContainerRepository $houndContainerRepository)
    {
        if ($houndContainerRepository->store($createContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scan Packages');
            return redirect()->route('warehouse.hound_containers.index');
        }
        session()->flash('alert-danger', $houndContainerRepository->getError());
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
        
        return view('admin.warehouse.houndContainers.edit',compact('container'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $updateContainerRequest, HoundContainerRepository $houndContainerRepository)
    {
        $container = Container::find($updateContainerRequest->id);

        if ( $container->response != 0 ){
            abort(405);
        }
        
        if ( $container = $houndContainerRepository->update($container, $updateContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scan Packages');
            
            $containers = $houndContainerRepository->get();        
            return view('admin.warehouse.houndContainers.index', compact('containers'));
        }
        session()->flash('alert-danger', $houndContainerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($container, HoundContainerRepository $houndContainerRepository)
    {
        $container = Container::find($container);
        if ( $container->respone != 0 ){
            abort(403,'Cannot Delete Container registered on Correios Chile.');
        }
        if ( $container = $houndContainerRepository->delete($container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.houndContainers.index');
        }

        session()->flash('alert-danger', $houndContainerRepository->getError());
        return back()->withInput();        
    }

}
