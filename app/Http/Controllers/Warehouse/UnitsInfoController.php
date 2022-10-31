<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\Unit\UnitRequest;
use App\Repositories\Warehouse\UnitInfoRepository;

class UnitsInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, UnitInfoRepository $repository)
    {
        $type = $request->type;
        $unitInfo = [];
        $rules = [
            'type'      => 'required',
        ];
        
        if($request->type != 'units_return'){
            $rules['start_date']= 'required|date';
            $rules['end_date']= 'required|date|after:start_date';
        }

        if($request->type == 'departure_info'){
            $rules['unitCode']        = 'required';
            $rules['flightNo']        = 'required';
            $rules['airlineCode']     = 'required';
            $rules['deprAirportCode'] = 'required';
            $rules['arrvAirportCode'] = 'required';
            $rules['destCountryCode'] = 'required';
        }
        $dateVali = $this->validate($request,$rules);
        
        if($type){
            $unitInfo = $repository->getUnitInfo($request);
        }
        \Log::info('dateVali');
        \Log::info($dateVali);
        return view('admin.warehouse.unitInfo.create', compact('unitInfo', 'type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //        
    }

}
