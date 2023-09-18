<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GSSRate;
use App\Models\ShippingService;
use App\Models\Country;
use App\Http\Requests\GSSRateRequest; 
use App\Http\Requests\Admin\Service\UpdateService; 
use App\Repositories\GSSRateRepository;
use Illuminate\Support\Facades\Auth;


class GSSRateController extends Controller
{
    public function __construct()
    {  
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GSSRateRepository $repository)
    {
        $this->authorizeResource(GSSRate::class);
        $gssRates = $repository->get();
        return view('admin.gss_rates.index', compact('gssRates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorizeResource(GSSRate::class);

        $countries = Country::all();
        $shippingServices  = ShippingService::all();
        return view('admin.gss_rates.create',compact(['countries','shippingServices']));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GSSRateRequest $request, GSSRateRepository $repository)
    {
        $this->authorizeResource(GSSRate::class);
        
        if ( $repository->store($request) ){
            session()->flash('alert-success', 'GSS Rate Created');
            return  redirect()->route('admin.gss-rates.index');
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
    public function edit(GSSRate $gssRate)
    {
        
        $this->authorizeResource(GSSRate::class);
        $countries = Country::all();
        $shippingServices  = ShippingService::all();
    
        return view('admin.gss_rates.edit', compact(['gssRate','countries','shippingServices'])); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GSSRateRequest $request, GSSRate $gssRate, GSSRateRepository $repository)
    {
        $this->authorizeResource(GSSRate::class);

        if ( $repository->update($request, $gssRate) ){
            session()->flash('alert-success', 'GSS Rate Updated');
            return  redirect()->route('admin.gss-rates.index');
        }

        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(GSSRate $gssRate, GSSRateRepository $repository)
    {   
        $this->authorizeResource(GSSRate::class);
         if ($repository->delete($gssRate) ){
            session()->flash('alert-success', 'GSS Rate Deleted');
            return  redirect()->route('admin.gss-rates.index');
        }
  
        return back();
    }
}
