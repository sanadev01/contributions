<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\TotalExpressContainerRepository;
use App\Http\Requests\Warehouse\Container\CreateContainerRequest;
use App\Http\Requests\Warehouse\Container\UpdateContainerRequest;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Auth;

class ContainerFactoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $serviceSubClass = request('service_sub_class');
        $containers = Container::when(!Auth::user()->isAdmin(),function($query){
            $query->where('user_id',Auth::id());
        })->where('services_subclass_code', $serviceSubClass)
        ->latest()->paginate();
        
        return view('admin.warehouse.containers_factory.index', compact('containers','serviceSubClass'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $serviceSubClass = request('service_sub_class');
        $shippingServices = ShippingService::where('service_sub_class', $serviceSubClass)->get();
         
        return view('admin.warehouse.containers_factory.create',compact('shippingServices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $createContainerRequest, TotalExpressContainerRepository $totalExpressContainerRepository)
    {
        if ( $container = $totalExpressContainerRepository->store($createContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scan Packages');
            return redirect()->route('warehouse.totalexpress_containers.index');
        }
        session()->flash('alert-danger', $totalExpressContainerRepository->getError());
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
        
        return view('admin.warehouse.containers_factory.edit',compact('container'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $updateContainerRequest, TotalExpressContainerRepository $totalExpressContainerRepository)
    {
        $container = Container::find($updateContainerRequest->id);

        if ( $container->response != 0 ){
            abort(405);
        }
        
        if ( $container = $totalExpressContainerRepository->update($container, $updateContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scan Packages');
            return redirect()->route('warehouse.containers_factory.index');
        }
        session()->flash('alert-danger', $totalExpressContainerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($container, TotalExpressContainerRepository $totalExpressContainerRepository)
    {
        $container = Container::find($container);
        if ( $container->respone != 0 ){
            abort(403,'Cannot Delete Container registered on Correios Chile.');
        }
        if ( $container = $totalExpressContainerRepository->delete($container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.containers_factory.index');
        }

        session()->flash('alert-danger', $totalExpressContainerRepository->getError());
        return back()->withInput();        
    }

}
