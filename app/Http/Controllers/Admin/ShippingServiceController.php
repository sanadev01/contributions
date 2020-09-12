<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Requests\Admin\Service\CreateServiceShipping;


class ShippingServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shippingservices = ShippingService::all();
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
    public function store(CreateServiceShipping $request)
    {
        ShippingService::create(
            $request->only([
                'name',
                'max_length_allowed',
                'max_width_allowed',
                'min_width_allowed',
                'min_length_allowed',
                'max_sum_of_all_sides',
                'contains_battery_charges',
                'contains_perfume_charges',
                'contains_flammable_liquid_charges',
                'active',
            ])
        );

        session()->flash('alert-success', 'shippingservice.Created');
        return  redirect()->route('admin.shipping-services.index');
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
    public function update(CreateServiceShipping $request, ShippingService $shippingService)
    {
        $shippingService->update(
            $request->only([
                'name',
                'max_length_allowed',
                'max_width_allowed',
                'min_width_allowed',
                'min_length_allowed',
                'max_sum_of_all_sides',
                'contains_battery_charges',
                'contains_perfume_charges',
                'contains_flammable_liquid_charges',
                'active',
            ])
        );

        session()->flash('alert-success', 'shippingservice.Updated');
        return  redirect()->route('admin.shipping-services.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShippingService $shippingService)
    {   
        $shippingService->delete();

        session()->flash('alert-success', 'shippingservice.Deleted');
        return  redirect()->route('admin.shipping-services.index');
    }
}
