<?php

namespace App\Http\Controllers\Admin\Inventory;

use Exception;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Inventory\ProductRepository;

class ProductOrderController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepository = $productRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $products = $this->productRepository->getProductsByIds(json_decode($request->data));

        return view('admin.inventory.order.create-sale', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'items.*.quantity' => 'required|gt:0',
        ];
        if ( Auth::user()->isAdmin() ){
            $rules['user_id'] = 'required|exists:users,id';
        }
        $this->validate($request, $rules);
        
        if($this->productRepository->createOrder($request))
        {
            session()->flash('alert-success','Sale Order Created Successfull');
            return redirect()->route('admin.inventory.product.index');
        }
        
        session()->flash('alert-danger', $this->productRepository->getError());
        return redirect()->route('admin.inventory.product.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product_order, ProductRepository $repository)
    {
        $parcel = $repository->storeSingleOrder($product_order);
        if($parcel){
            return redirect()->route('admin.parcels.edit',$parcel);
        }
        session()->flash('alert-danger','Something Went wrong please check Product');
        return redirect()->back();
            // return redirect()->route('admin.orders.sender.index',$order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product_order)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
