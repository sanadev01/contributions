<?php

namespace App\Repositories\Inventory;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductRepository
{
    
    public function get(Request $request,$paginate = true,$pageSize=50)
    {
        $query = Product::has('user');

        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }
        
        if ( $request->date ){
            $query->where(function($query) use($request){
                return $query->where('created_at', 'LIKE', "%{$request->date}%");
            });
        }

        if ( $request->name ){
            $query->where(function($query) use($request){
                return $query->where('name', 'LIKE', "%{$request->name}%");
            });
        }

        if ( $request->user ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->user}%");
            });
        } 
   
        if ( $request->price ){
            $query->where(function($query) use($request){
                return $query->where('price', 'LIKE', "%{$request->price}%");
            });
        }

        if ( $request->sku ){
            $query->where(function($query) use($request){
                return $query->where('sku',"{$request->sku}");
            });
        }
        
        if ( $request->status){
            $query->where(function($query) use($request){
                return $query->where('status',$request->status);
            });
        }

      

        $products = $query;

        return $paginate ? $products->paginate($pageSize) : $products->get();
    }


    public function store(Request $request)
    {
        $product                = new Product();
        $product->user_id       = Auth::user()->isAdmin()? $request->user_id: auth()->id();
        $product->name          = $request->name;
        $product->price         = $request->price;
        $product->sku           = $request->sku;
        $product->status        = 'pending';

        $product->merchant      = $request->merchant;
        $product->carrier       = $request->carrier;
        $product->tracking_id   = $request->tracking_id;

        $product->order_date    = $request->order_date;
        $product->sh_code       = $request->sh_code;
        $product->description   = $request->description;
        $product->quantity      = $request->quantity;

        $product->weight        = $request->weight;
        $product->length        = $request->length;
        $product->width         = $request->width ;
        $product->height        = $request->height;
        
        $product->warehouse_number= $request->whr_number;
        $product->measurement_unit= $request->unit;
        
        $product->save();

        if ( $request->hasFile('invoiceFile') ){
            $product->attachInvoice( $request->file('invoiceFile') );
        }
        
        return true;
    }
   
    public function update(Request $request, Product $product)
    {
        $product->user_id       = Auth::user()->isAdmin()? $request->user_id: auth()->id();
        $product->name          = $request->name;
        $product->price         = $request->price;
        $product->sku           = $request->sku;
        $product->status        = 'pending';

        $product->merchant      = $request->merchant;
        $product->carrier       = $request->carrier;
        $product->tracking_id   = $request->tracking_id;

        $product->order_date    = $request->order_date;
        $product->sh_code       = $request->sh_code;
        $product->description   = $request->description;
        $product->quantity      = $request->quantity;

        $product->weight        = $request->weight;
        $product->length        = $request->length;
        $product->width         = $request->width ;
        $product->height        = $request->height;
        
        $product->warehouse_number= $request->whr_number;
        $product->measurement_unit= $request->unit;
        
        $product->save();

        if ( $request->hasFile('invoiceFile') ){
            $product->attachInvoice( $request->file('invoiceFile') );
        }
        
        return true;
    }

    public function getProductForExport()
    {   
        $query = Product::has('user');

        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }
        return $query->get();
    }

    public function storeOrder($productOrder)
    {
        DB::beginTransaction();

        try {
            
            $order = $productOrder->orders()->create([
                "user_id" => $productOrder->user_id,
                "merchant" => $productOrder->merchant,
                "carrier" => $productOrder->carrier,
                "tracking_id" => $productOrder->tracking_id,

                "weight" => $productOrder->weight,
                "length" => $productOrder->length,
                "width" => $productOrder->width,
                "height" => $productOrder->height,
                "measurement_unit" => $productOrder->measurement_unit,
                "status" => Order::STATUS_NEEDS_PROCESSING,
                "order_date" => now(),

            ]);

            $order->update([
                "warehouse_number" => "HD-{$order->id}",
            ]);

            $this->addItem($order, $productOrder);

            $productOrder->update([
                "quantity" => $productOrder->quantity -1,
            ]);

            DB::commit();

            return $order;

        } catch (\Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function addItem($order, $productOrder)
    {
        $order->items()->create([
            "quantity" => 1,
            "value" => $productOrder->price,
            "description" => $productOrder->description,
            "sh_code" => $productOrder->sh_code,
        ]);
    }


}
