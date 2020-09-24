<?php

namespace App\Http\Controllers\Admin\Consolidation;

use App\Http\Controllers\Controller;
use App\Models\HandlingService;
use App\Models\Order;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $parcel)
    {
        $parcel->load('services');
        $services = HandlingService::query()->active()->get();
        return view('admin.consolidation.services',compact('services','parcel'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Order $parcel)
    {
        $parcel->syncServices($request->get('services',[]));
        session()->flash('alert-success', 'Services Request Done.');
        return \redirect()->route('admin.parcels.index');
    }
}
