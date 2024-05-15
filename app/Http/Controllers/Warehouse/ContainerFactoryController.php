<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\TotalExpressContainerRepository;
use App\Http\Requests\Warehouse\Container\CreateContainerRequest;
use App\Http\Requests\Warehouse\Container\UpdateContainerFactoryRequest;
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
        $containers = Container::when(!Auth::user()->isAdmin(), function ($query) {
            $query->where('user_id', Auth::id());
        })->where('services_subclass_code', $serviceSubClass)->latest()->paginate();
        $shippingServiceNotExists = config("shippingServices.correios.sub_classess.$serviceSubClass") == null;
        if ($shippingServiceNotExists) {
            abort(404);
        }
        return view('admin.warehouse.containers_factory.index', compact('containers', 'serviceSubClass'));
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
        if ($shippingServices->isEmpty()){
            abort(404);
        }
        return view('admin.warehouse.containers_factory.create', compact('shippingServices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $request)
    { 
        try {
            $shippingServices = ShippingService::where('service_sub_class',  $request->services_subclass_code)->get();
           
            if ($shippingServices->isEmpty()){
                abort(404);
            }
            $container =  Container::create([
                'user_id' => Auth::id(),
                'dispatch_number' => 0,
                'origin_country' => 'US',
                'origin_operator_name' => 'HERC',
                'postal_category_code' => 'A',
                // 'seal_no' => $request->seal_no,
                // 'destination_operator_name' => $request->destination_operator_name,
                // 'unit_type' => $request->unit_type,
                // 'services_subclass_code' => $request->services_subclass_code
            ]+request()->all());

            $container->update([
                'dispatch_number' => $container->id
            ]);

            session()->flash('alert-success', 'Container Saved Please Scan Packages');
            return redirect()->route('warehouse.containers_factory.index',['service_sub_class'=>$request->services_subclass_code]); 

        } catch (\Exception $ex) { 
            session()->flash('alert-danger', $ex->getMessage());
            return back()->withInput();
        }

         
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
        if ($container->response != 0) {
            abort(405);
        }
        return view('admin.warehouse.containers_factory.edit', compact('container'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerFactoryRequest $request)
    {
        $container = Container::find($request->id);

        if ($container->response != 0) {
            abort(405);
        }
        try {
            $container->update([
                'seal_no' => $request->seal_no,
                'unit_type' => $request->unit_type
            ]);
            session()->flash('alert-success', 'Container Saved Please Scan Packages'); 
            return redirect()->route('warehouse.containers_factory.index',['service_sub_class'=>$container->services_subclass_code]); 
        } catch (\Exception $ex) { 
            session()->flash('alert-danger',$ex->getMessage() );
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($container)
    {
        $container = Container::find($container);
        if ($container->respone != 0) {
            abort(403, 'Cannot Delete Container registered on Correios Chile.');
        }
        try {
            $subClass=$container->services_subclass_code;
            $container->deliveryBills()->delete();
            $container->orders()->sync([]);
            $container->delete();
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.containers_factory.index',['service_sub_class'=>$subClass]);
        } catch (\Exception $ex){
            session()->flash('alert-danger', $ex->getMessage());
            return back()->withInput();
        }
    }
}
