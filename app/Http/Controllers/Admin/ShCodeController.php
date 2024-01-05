<?php

namespace App\Http\Controllers\Admin;

use App\Models\ShCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ShcodeRepository;

class ShCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.shcode.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.shcode.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ShcodeRepository $repository)
    {
        $rules = [
            'code' => 'required',
            'en' => 'required',
            'pt' => 'required',
            'sp' => 'required',
        ];
        $this->validate($request, $rules);

        if ( $repository->store($request) ){
            session()->flash('alert-success','Shcode.Created');
        }

        return back();
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
    public function edit(ShCode $shcode)
    {
        return view('admin.shcode.edit',compact('shcode'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShCode $shcode, ShcodeRepository $repository)
    {

        if ( $repository->update($request, $shcode) ){
            session()->flash('alert-success','Shcode Updated');
            return redirect()->route('admin.shcode.index');
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShCode $shcode, ShcodeRepository $repository)
    {

        if ( $repository->delete($shcode) ){
            session()->flash('alert-success','Shcode Deleted');
        }

        return back();
    }
}
