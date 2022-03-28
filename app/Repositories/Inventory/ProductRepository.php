<?php

namespace App\Repositories\Inventory;

use Exception;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\InventoryOrderPlaced;
use App\Services\Converters\UnitsConverter;
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

        $products = $query;

        return $paginate ? $products->paginate($pageSize) : $products->get();
    }

    public function getProductsByIds($Ids)
    {
        return Product::whereIn('id', $Ids)->get();
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
                'customer_reference' => $productOrder->sh_code,
                'carrier' => $productOrder->order,
                'tracking_id' => $productOrder->description,
                'weight' => ($productOrder->measurement_unit == 'kg/cm') ? $productOrder->weight : UnitsConverter::poundToKg($productOrder->weight),
                'length' => 5,
                'width' => 4,
                'height' => 5,
                'measurement_unit' => 'kg/cm',
                'order_date' => now(),

            ]);

            $order->update([
                'warehouse_number' => "HD-{$order->id}",
                'weight' => number_format($order->weight + ($order->weight * Product::WEIGHT_PERCENTAGE), 2),
            ]);

            if ($productOrder->quantity > 0) {
                $productOrder->update([
                    'quantity' => $productOrder->quantity -1,
                ]);
            }

            $order->items()->create([
                'quantity' => 1,
                'sh_code' =>$productOrder->sh_code,
                'value' => $productOrder->price,
                'description' => $productOrder->description,
            ]);

            try {
                Mail::send(new InventoryOrderPlaced($order));
            } catch (\Exception $ex) {
                Log::info('Inventory Order email error: '.$ex->getMessage());
            }

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
                'customer_reference' => $this->setShCodes($request->order_items),
                'carrier' => $this->setOrderNumbers($request->order_items),
                'tracking_id' => $this->setOrderDescriptions($request->order_items),
                'weight' => $this->setOrderWeights($request->order_items),
                'length' => 5,
                'width' => 4,
                'height' => 5,
                'measurement_unit' => 'kg/cm',
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

            try {
                Mail::send(new InventoryOrderPlaced($order));
            } catch (\Exception $ex) {
                Log::info('Inventory Order email error: '.$ex->getMessage());
            }

            return $order;
        } catch (\Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
        }

    }

    public function createOrder($request)
    {
        DB::beginTransaction();
        try {
           $order = Order::create([
                'user_id' => Auth::user()->isAdmin()? $request->user_id: auth()->id(),
                'status' => Order::STATUS_INVENTORY,
            ]);

            $order->update([
                'warehouse_number' => "HD-{$order->id}"
            ]);

            foreach($request->ids as $key=> $productId)
            {
                $product = Product::find($productId);
            
                if ($product->quantity > 0) {
                    $order->products()->attach($product);
                    $product->update([
                        'quantity' => $product->quantity - $request->items[$key]['quantity'],
                    ]);
                    $order->items()->create([
                        'quantity' => $request->items[$key]['quantity'],
                        'sh_code' =>$product->sh_code,
                        'value' => $product->price,
                        'description' => $product->description,
                    ]);
                }
            }

            if ($order->products->isNotEmpty()) {
                $items = $order->products->toArray();

                $order->update([
                    'customer_reference' => $this->setShCodes($items),
                    'carrier' => $this->setOrderNumbers($items),
                    'tracking_id' => $this->setOrderDescriptions($items),
                ]);
            }

            DB::commit();

            return true;

        } catch (Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function getPickupOrders()
    {
        $query = Order::query();
        $query = (auth()->user()->isAdmin()) ? $query : $query->where('user_id', auth()->user()->id);

        $orders = $query->where('status', Order::STATUS_INVENTORY)->with(
                ['user' => function ($query){ $query->select('id', 'name', 'pobox_number');}, 
                            'products' => function($query){ $query->select('sku','name','id');}
                            ])->select('warehouse_number','user_id', 'status', 'id')->get();

        return $orders;
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

    private function setShCodes($items)
    {
        $shCodes = [];
        foreach ($items as $item) {
            $shCodes[] = $item['sh_code'];
        }

        return implode(',', $shCodes);
    }

    private function setOrderNumbers($items)
    {
        $orderNumbers = [];
        foreach ($items as $item) {
            $orderNumbers[] = $item['order'];
        }
        return implode(',', $orderNumbers);
    }

    private function setOrderDescriptions($items)
    {
        $orderDescriptions = [];
        foreach ($items as $item) {
            $orderDescriptions[] = $item['description'];
        }
        return implode(',', $orderDescriptions);
    }

    private function setOrderWeights($items)
    {
        $weight = 0;

        foreach ($items as $item) {
            $weight += $item['total_weight'];
        }

        $totalWeight = $weight + ($weight * Product::WEIGHT_PERCENTAGE);

        return number_format($totalWeight, 2);
    }

}
