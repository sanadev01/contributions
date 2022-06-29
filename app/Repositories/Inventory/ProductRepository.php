<?php

namespace App\Repositories\Inventory;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Excel\Import\ProductImportService;

class ProductRepository
{
    public $error;
    
    public function get(Request $request)
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
        
        if ( $request->quantity ){
            $query->where(function($query) use($request){
                return $query->where('quantity', 'LIKE', "%{$request->quantity}%");
            });
        }
        
        if ( $request->status ){
            $query->where(function($query) use($request){
                return $query->where('status', 'LIKE', "%{$request->status}%");
            });
        }

        if ( $request->sku ){
            $query->where(function($query) use($request){
                return $query->where('sku',"{$request->sku}");
            });
        }
        
        if ( $request->weight ){
            $query->where(function($query) use($request){
                return $query->where('weight',"{$request->weight}");
            });
        }
        
        if ( $request->unit ){
            $query->where(function($query) use($request){
                return $query->where('measurement_unit',"{$request->unit}");
            });
        }
        
        if ( $request->description ){
            $query->where(function($query) use($request){
                return $query->where('description',"{$request->description}");
            });
        }
        
        if ( $request->expdate ){
            $query->where(function($query) use($request){
                return $query->where('exp_date',"{$request->expdate}");
            });
        }

        return $products = $query;
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
        $product->inventory_value = $request->quantity * $request->price;
        $product->min_quantity   = $request->min_quantity;
        $product->max_quantity   = $request->max_quantity;
        $product->discontinued   = $request->discontinued;
        $product->store_day      = $request->store_day;
        $product->location       = $request->location;
        $product->sh_code        = $request->sh_code;
        $product->weight         = $request->weight;
        $product->measurement_unit = $request->measurement_unit;
        $product->exp_date       = $request->exp_date;
        
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
            'sku' => $request->sku,
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
            'inventory_value' => $request->quantity * $request->price,
            'min_quantity' => $request->min_quantity,
            'max_quantity' => $request->max_quantity,
            'discontinued' => $request->discontinued,
            'store_day' => $request->store_day,
            'location' => $request->location,
            'sh_code' => $request->sh_code,
            'weight' => $request->weight,
            'measurement_unit' => $request->measurement_unit,
            'exp_date' => $request->exp_date,
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
