<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Inventory\ProductRepository;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = '';
        return view('admin.inventory.product.index',compact('status'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.inventory.product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ProductRepository $repository)
    {
        $this->validate($request,[
            "name"          => "required",
            "price"         => "required",
            "sku"           => "required",
            // "status"        => "required",
            "description"   => "required",
        ]);
        
        if ( $repository->store($request) ){
            session()->flash('alert-success','Product Saved Successfull');
            return redirect()->back();
        }
        return back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($status)
    {
        return view('admin.inventory.product.index',compact('status'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('admin.inventory.product.edit',compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product, ProductRepository $repository)
    {
        $this->validate($request,[
            "name"          => "required",
            "price"         => "required",
            "sku"           => "required",
            // "status"        => "required",
            "description"   => "required",
        ]);

        if ($repository->update($request,$product) ){
            session()->flash('alert-success','Product Update');
            return redirect()->route('admin.inventory.product.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        session()->flash('alert-success','Product Deleted Successfull');
        return redirect()->back();
    }

    /**
     * StatusUpdate a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function StatusUpdate(Request $request)
    {
        $product = Product::find($request->product_id);

        if ( $product ){
            $product->status = $request->status;
            $product->save();
            return apiResponse(true,"Updated");
        }
        return apiResponse(false,"Error while update");
    }

}
