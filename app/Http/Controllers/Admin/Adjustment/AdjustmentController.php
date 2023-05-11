<?php

namespace App\Http\Controllers\Admin\Adjustment;

use App\Models\Tax;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Repositories\AdjustmentRepository; 

class AdjustmentController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $adjustmentRepository;
    public function __construct()
    {
        $this->adjustmentRepository = new AdjustmentRepository();
    }
    public function create()
    {
        $this->authorize('create_adjustment', Tax::class);
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
        $this->authorize('create_adjustment', Tax::class);
        $request->validate([
            'adjustment' => 'required|numeric|min:.001',
            'user_id' => 'required|numeric',
        ]);
        $response = $this->adjustmentRepository->store($request);
        if (is_bool($response) && $response) {
            session()->flash('alert-success', 'Adjustment has been added successfully');
            return redirect()->route('admin.tax.index');
        } else {
            return back()->withInput()->withErrors($response);
        };
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
        $this->authorize('update_adjustment', $tax);
        return view('admin.adjustment.edit', compact('tax'));
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
            'adjustment' => 'required|numeric|min:0', 
        ]);
        $tax = Tax::find($id);

        $this->authorize('update_adjustment', $tax);
        $response = $this->adjustmentRepository->update($request, $tax); 
        if ($response) {
            session()->flash('alert-success', 'Adjustment Updated');
            return redirect()->route('admin.tax.index');
        }
        session()->flash('alert-danger', 'Error While Update Tax! Check Your Account Balance');
        return back()->withInput();
    }
}
