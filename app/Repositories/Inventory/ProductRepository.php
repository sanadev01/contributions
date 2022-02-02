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
        $product                 = new Product();
        $product->user_id        = Auth::user()->isAdmin()? $request->user_id: auth()->id();
        $product->name           = $request->name;
        $product->price          = $request->price;
        $product->sku            = $request->sku;
        $product->status         = 'pending';
        $product->order          = $request->order;
        $product->category       = $request->category;
        $product->brand          = $request->brand;
        $product->manufacturer   = $request->manufacturer;
        $product->barcode        = $request->barcode;
        $product->description    = $request->description;
        $product->quantity       = $request->quantity;
        $product->item           = $request->item;
        $product->lot            = $request->lot ;
        $product->unit           = $request->unit ;
        $product->case           = $request->case;
        $product->inventory_alue = $request->inventory_alue;
        $product->min_quantity   = $request->min_quantity;
        $product->max_quantity   = $request->max_quantity;
        $product->discontinued   = $request->discontinued;
        $product->stor_day       = $request->stor_day;
        $product->location       = $request->location;
        
        $product->save();

        if ( $request->hasFile('invoiceFile') ){
            $product->attachInvoice( $request->file('invoiceFile') );
        }
        
        return true;
    }
   
    public function update(Request $request, Product $product)
    {
        $product                 = new Product();
        $product->user_id        = Auth::user()->isAdmin()? $request->user_id: auth()->id();
        $product->name           = $request->name;
        $product->price          = $request->price;
        $product->sku            = $request->sku;
        $product->status         = 'pending';
        $product->order          = $request->order;
        $product->category       = $request->category;
        $product->brand          = $request->brand;
        $product->manufacturer   = $request->manufacturer;
        $product->barcode        = $request->barcode;
        $product->description    = $request->description;
        $product->quantity       = $request->quantity;
        $product->item           = $request->item;
        $product->lot            = $request->lot ;
        $product->unit           = $request->unit ;
        $product->case           = $request->case;
        $product->inventory_alue = $request->inventory_alue;
        $product->min_quantity   = $request->min_quantity;
        $product->max_quantity   = $request->max_quantity;
        $product->discontinued   = $request->discontinued;
        $product->stor_day       = $request->stor_day;
        $product->location       = $request->location;
        
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
                "status" => Order::STATUS_PREALERT_READY,
                "order_date" => now(),

            ]);

            $order->update([
                "warehouse_number" => "HD-{$order->id}",
            ]);

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

}
