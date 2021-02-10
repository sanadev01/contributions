<?php

namespace App\Http\Controllers\Admin\Rates;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use Illuminate\Http\Request;

class FixedChargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('updatefixedRates',Rate::class);

        return view('admin.rates.fixed-charges.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('updatefixedRates',Rate::class);
        
        saveSetting("consolidation_charges",$request->consolidation_charges);
        session()->flash('alert-success','Charges Saved');
        return back();
    }
}
