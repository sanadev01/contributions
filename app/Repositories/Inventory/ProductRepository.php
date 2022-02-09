<?php

namespace App\Repositories\Inventory;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Excel\Import\ProductImportService;

class ProductRepository
{
    public $error;
    
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
        $product->sku            = strtoupper($request->sku);
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
        $product->inventory_value = $request->inventory_value;
        $product->min_quantity   = $request->min_quantity;
        $product->max_quantity   = $request->max_quantity;
        $product->discontinued   = $request->discontinued;
        $product->store_day       = $request->store_day;
        $product->location       = $request->location;
        $product->sh_code        = $request->sh_code;
        
        $product->save();

        if ( $request->hasFile('invoiceFile') ){
            $product->attachInvoice( $request->file('invoiceFile') );
        }
        
        return true;
    }
   
    public function update(Request $request, Product $product)
    {
        $product->update([
            'user_id' => Auth::user()->isAdmin()? $request->user_id: auth()->id(),
            'name' => $request->name,
            'price' => $request->price,
            'sku' => strtoupper($request->sku),
            'status' => $product->status,
            'order' => $request->order,
            'category' => $request->category,
            'brand' => $request->brand,
            'manufacturer' => $request->manufacturer,
            'barcode' => $request->barcode,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'item' => $request->item,
            'lot' => $request->lot,
            'unit' => $request->unit,
            'case' => $request->case,
            'inventory_value' => $request->inventory_value,
            'min_quantity' => $request->min_quantity,
            'max_quantity' => $request->max_quantity,
            'discontinued' => $request->discontinued,
            'store_day' => $request->store_day,
            'location' => $request->location,
        ]);
        
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

    public function getProductBySku($sku, $user_id)
    {
        $query = Product::query();

        if ($user_id) {
            $product = $query->where([
                ['sku', $sku],
                ['status', 'approved'],
                ['quantity', '>', 0],
                ['user_id', $user_id],
            ])->first();

            if (!$product) {
                $this->error = 'Product not found against this user';
            }

            return $product;
        }

        $product = $query->where([
            ['sku', $sku],
            ['status', 'approved'],
            ['quantity', '>', 0],
        ])->first();

        if (!$product) {
            $this->error = 'Product not found';
        }

        return $product;
    }

    public function storeSingleOrder($productOrder)
    {
        DB::beginTransaction();

        try {
            
            $order = $productOrder->orders()->create([
                'user_id' => $productOrder->user_id,
                'status' => Order::STATUS_PREALERT_READY,
                'order_date' => now(),

            ]);

            $order->update([
                'warehouse_number' => "HD-{$order->id}",
            ]);

            $productOrder->update([
                'quantity' => $productOrder->quantity -1,
            ]);

            $order->items()->create([
                'quantity' => 1,
                'sh_code' =>$productOrder->sh_code,
                'value' => $productOrder->price,
                'description' => $productOrder->description,
            ]);

            DB::commit();

            return $order;

        } catch (\Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function placeInventoryOrder(Request $request)
    {
        DB::beginTransaction();
        try {

            $order = Order::create([
                'user_id' => Auth::user()->isAdmin()? $request->user_id: auth()->id(),
                'status' => Order::STATUS_PREALERT_TRANSIT,
                'order_date' => now(),
            ]);
            $order->update([
                'warehouse_number' => $order->getTempWhrNumber(),
            ]);

            foreach ($request->order_items as $item) {

               $product = Product::find($item['id']);
               $order->products()->attach($product);
                $product->update([
                     'quantity' => $product->quantity - $item['quantity'],
                ]);

                $order->items()->create([
                    'quantity' => $item['quantity'],
                    'sh_code' =>$product->sh_code,
                    'value' => $product->price,
                    'description' => $product->description,
                ]);
            }

            DB::commit();

            return $order;
        } catch (\Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
        }

    }

    public function getError()
    {
        return $this->error;
    }
    
    public function importProduct($request)
    {
        $importExcelService = new ProductImportService($request->file('excel_file'),$request);
        $response = $importExcelService->handle();
        return $response;
    }
    

}
