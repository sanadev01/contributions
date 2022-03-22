<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Inventory\ProductRepository;
use App\Http\Requests\Product\ProductCreateRequest;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    public function index(Request $request)
    {
        $products = $this->productRepository->get($request);

        if ($products->isEmpty()) {
           return apiResponse(false, 'no product found');
        }

        return apiResponse(true, 'products found', $products);
    }

    public function show(Product $product)
    {
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
}
