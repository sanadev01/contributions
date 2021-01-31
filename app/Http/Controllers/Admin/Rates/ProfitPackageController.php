<?php

namespace App\Http\Controllers\Admin\Rates;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfitPackage\CreateRequest;
use App\Http\Requests\Admin\ProfitPackage\UpdateRequest;
use App\Models\ProfitPackage;
use App\Models\User;
use App\Repositories\ProfitPackageRepository;
use Illuminate\Http\Request; 

class ProfitPackageController extends Controller
{   
    public function __construct()
    {
        $this->authorizeResource(ProfitPackage::class);
    } 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProfitPackageRepository $repository)
    {
        $packages = $repository->get();
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
    public function store(CreateRequest $request, ProfitPackageRepository $repository)
    {   
        if ( $repository->store($request) ){
            session()->flash('alert-success',"profitpackage.created");
            return redirect()->route('admin.rates.profit-packages.index');
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
    public function update(UpdateRequest $request, ProfitPackage $profitPackage, ProfitPackageRepository $repository)
    {   

        if ( $repository->update($request,$profitPackage) ){
            session()->flash('alert-success','profitpackage.updated');
            return redirect()->route('admin.rates.profit-packages.index');
        }

        return back()->withInput();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProfitPackage $profitPackage, ProfitPackageRepository $repository)
    {   

        if ( $repository->delete($profitPackage) ){
            session()->flash('alert-success','profitpackage.deleted');
        }
  
        return back();
    }
}
