<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HandlingService;
use App\Http\Requests\Admin\Service\CreateService; 
use App\Http\Requests\Admin\Service\UpdateService; 
use App\Repositories\HandlingServiceRepository;


class HandlingServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HandlingServiceRepository $repository)
    {
        $services = $repository->get();
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.services.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateService $request, HandlingServiceRepository $repository)
    {
        
        if ( $repository->store($request) ){
            session()->flash('alert-success', 'handlingservice.Created');
            return  redirect()->route('admin.services.index');
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
    public function edit(HandlingService $service)
    {

        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateService $request, HandlingService $handlingService, HandlingServiceRepository $repository)
    {
        if ( $repository->update($request, $handlingService) ){
            session()->flash('alert-success', 'handlingservice.Updated');
            return  redirect()->route('admin.services.index');
        }

        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(HandlingService $service, HandlingServiceRepository $repository)
    {   
         if ( $repository->delete($service) ){
            session()->flash('alert-success', 'handlingservice.Deleted');
            return  redirect()->route('admin.services.index');
        }
  
        return back();
    }
}
