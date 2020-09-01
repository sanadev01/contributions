<?php

namespace App\Http\Controllers\Admin\Rates;

use App\Http\Controllers\Controller;
use App\Models\ProfitPackage;
use App\Models\User;
use Illuminate\Http\Request;

class ProfitPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packages = ProfitPackage::get();
        return view('admin.rates.profit-packages.index',compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.rates.profit-packages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'package_name' => 'required|string|max:90',
        ]);

        $profitPackage = ProfitPackage::create([
            'name' => $request->package_name,
        ]);


        session()->flash('alert-success','Package Created');

        return redirect()->route('admin.rates.profit-packages.index');
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
    public function edit(ProfitPackage $profitPackage)
    {
        return view('admin.rates.profit-packages.edit',compact('profitPackage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProfitPackage $profitPackage)
    {
        $this->validate($request,[
            'package_name' => 'required|string|max:90',
        ]);

        $profitPackage->update([
            'name' => $request->package_name
        ]);

        session()->flash('alert-success','Package Updated');

        return redirect()->route('admin.rates.profit-packages.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProfitPackage $profitPackage)
    {
        // User::where('package_id', $profitPackage->id)->update('package_id',null);
        $profitPackage->delete();

        session('alert-success','Package Deleted');

        return back();
    }
}
