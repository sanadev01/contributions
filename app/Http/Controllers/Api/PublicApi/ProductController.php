<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Inventory\ProductRepository;
use App\Http\Requests\Product\ProductCreateRequest;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    public function index(Request $request)
    {
        $productsQuery = $this->productRepository->get($request); 
        $products = $productsQuery->get();
        if ($products->isEmpty()) {
           return apiResponse(false, 'no product found');
        }

        return apiResponse(true, 'products found', $products);
    }

    public function show(Product $product)
    {
        if(Auth::id() !=$product->user_id){
            return apiResponse(false,'Product not found');
        }
        $this->authorize('view', $product);
        
        return apiResponse(true, 'product found', $product);
    }

    public function store(ProductCreateRequest $request)
    {
        $this->authorize('create', Product::class);

        if ( $this->productRepository->store($request) ){
            return apiResponse(true, 'Product Saved Successfull');
        }

        return apiResponse(false, 'Product Not Saved');
    }

    public function updateItems(Request $request, Order $parcel)
    {
        DB::beginTransaction();

        try {
            $parcel->items()->delete();
            $isBattery = false;
            $isPerfume = false;
            foreach ($request->get('products',[]) as $product) {
                
                if(optional($product)['is_battery']){
                    $isBattery = true;
                }
                if(optional($product)['is_perfume']){
                    $isPerfume = true;
                }
                $parcel->items()->create([
                    "sh_code" => optional($product)['sh_code'],
                    "description" => optional($product)['description'],
                    "quantity" => optional($product)['quantity'],
                    "value" => optional($product)['value'],
                    "contains_battery" => optional($product)['is_battery'],
                    "contains_perfume" => optional($product)['is_perfume'],
                    "contains_flammable_liquid" => optional($product)['is_flameable'],
                ]);
            }
            if( $isBattery === true && $isPerfume === true){
                throw new \Exception("Please don't use battery and perfume in one parcels",500);
            }

            $orderValue = collect($request->get('products',[]))->sum(function($item){
                return $item['value'] * $item['quantity'];
            });

            $parcel->update([
                "order_value" => $orderValue,
            ]);

            DB::commit();
            return apiResponse(true,"Parcel Items Updated", OrderResource::make($parcel) );

        } catch (\Exception $ex) {
            DB::rollback();
           return apiResponse(false,$ex->getMessage());
        }
    }

}
