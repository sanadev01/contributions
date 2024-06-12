<?php

namespace App\Http\Controllers\Admin\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Excel\Export\ProductExport;
use App\Repositories\Inventory\ProductRepository;

class ProductExportController extends Controller
{
    public function index(ProductRepository $repository)
    {
        $product = $repository->getProductForExport();
        
        $exportProduct = new ProductExport($product);
        return $exportProduct->handle();
    }
}
