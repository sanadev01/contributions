<?php

namespace App\Http\Controllers\Admin\Inventory;

use Exception;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Inventory\OrderRepository;
use App\Repositories\Inventory\ProductRepository;

class ProductOrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
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
        $isSingle = false;
        $products = $this->orderRepository->getProductsByIds(json_decode($request->data));

        return view('admin.inventory.order.create-sale', compact('products','isSingle'));
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
        
        if($this->orderRepository->createOrder($request))
        {
            session()->flash('alert-success','Sale Order Created Successfull');
            return redirect()->route('admin.inventory.orders');
        }
        
        session()->flash('alert-danger', $this->orderRepository->getError());
        return redirect()->route('admin.inventory.product.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product_order)
    {
        $product = $product_order;
        $isSingle = true;

        return view('admin.inventory.order.create-sale', compact('product','isSingle'));
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
