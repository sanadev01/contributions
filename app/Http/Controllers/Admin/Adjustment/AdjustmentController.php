<?php

namespace App\Http\Controllers\Admin\Adjustment;

use App\Models\Tax;
use Illuminate\Http\Request;
use App\Repositories\TaxRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tax\TaxRequest;
use App\Http\Requests\Tax\TaxUpdateRequest;
use App\Models\Order;

class AdjustmentController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        $this->authorize('create', Tax::class);  
        return view('admin.adjustment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'adjustment' => 'required', 
            'user_id' => 'required|numeric', 
        ]); 
        $this->authorize('create', Tax::class); 

        Tax::create([
            'user_id' => $request->user_id, 
            'adjustment' => $request->adjustment, 
        ]);     
        
        session()->flash('alert-success', 'Adjustment has been added successfully');

        return redirect()->route('admin.tax.index'); 
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
        $tax = Tax::find($id);
        $this->authorize('update',$tax);
        return view('admin.adjustment.edit',compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        
        $request->validate([
            'adjustment' => 'required',  
        ]); 
        $tax = Tax::find($id);

        $this->authorize('update', $tax);
        $tax->update([
            'adjustment' => $request->adjustment, 
        ]); 

            session()->flash('alert-success', 'Adjustment Updated');
            return redirect()->route('admin.tax.index');
         
    }
}
