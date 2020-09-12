<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HandlingService;
use App\Http\Requests\Admin\Service\CreateService;

class HandlingServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = HandlingService::all();
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
    public function store(CreateService $request)
    {
        HandlingService::create(
            $request->only([
                'name',
                'cost',
                'price',
            ])
        );

        session()->flash('alert-success', 'handlingservice.Created');
        return  redirect()->route('admin.services.index');
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
    public function update(CreateService $request, HandlingService $service)
    {
        $service->update(
            $request->only([
                'name',
                'cost',
                'price',
            ])
        );

        session()->flash('alert-success', 'handlingservice.Updated');
        return  redirect()->route('admin.services.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(HandlingService $service)
    {   
        $service->delete();

        session()->flash('alert-success', 'handlingservice.Deleted');
        return  redirect()->route('admin.services.index');
    }
}
