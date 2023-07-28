<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Requests\Admin\Service\CreateShippingService; 
use App\Http\Requests\Admin\Service\UpdateShippingService; 
use App\Repositories\ShippingServiceRepository;
use Illuminate\Support\Facades\Artisan; 

class ShippingServiceController extends Controller
{   
    public function __construct()
    {
        $this->authorizeResource(ShippingService::class);
    } 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ShippingServiceRepository $repository)
    {
        Artisan::call('db:seed', [
            '--class' => 'ShippingServiceSeeder',
        ]);
        $shippingservices = $repository->get(); 
        return view('admin.shippingservices.index', compact('shippingservices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.shippingservices.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateShippingService $request, ShippingServiceRepository $repository)
    {

        if ($repository->store($request) ){
            session()->flash('alert-success', 'shippingservice.Created');
            return  redirect()->route('admin.shipping-services.index');
        }

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
    public function edit(ShippingService $shippingService)
    {
        return view('admin.shippingservices.edit', compact('shippingService'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateShippingService $request, ShippingService $shippingService, ShippingServiceRepository $repository)
    {
        if ($repository->update($request, $shippingService) ){

            session()->flash('alert-success', 'shippingservice.Updated');
            return  redirect()->route('admin.shipping-services.index');

        }

        return back()->withInput();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShippingService $shippingService, ShippingServiceRepository $repository)
    {   
        if ( $repository->delete($shippingService) ){
            session()->flash('alert-success', 'shippingservice.Deleted');
            return  redirect()->route('admin.shipping-services.index');
        }

        return back();
    }
}
