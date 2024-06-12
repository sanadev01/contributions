<?php

namespace App\Http\Livewire\Product;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Services\Converters\UnitsConverter;
use App\Repositories\Inventory\ProductRepository;

class Scan extends Component
{
    public $search;
    public $scannedProducts = [];
    public $productError;
    public $totalProducts;

    public $user_id;
    protected $quantity = 0;

    
    protected $rules = [
        'search' => 'required|min:4',
    ];

    public function render()
    {
        $this->totalProducts();
        return view('livewire.product.scan');
    }

    public function updatedSearch()
    {
        $this->validate();
        $this->search = strtoupper($this->search);
        $this->getProduct();
    }

    public function removeProduct($id)
    {
        $this->scannedProducts = array_filter($this->scannedProducts, function ($product) use ($id) {
            return $product['id'] != $id;
        });
        $this->scannedProducts = array_values($this->scannedProducts);
    }
    

    private function getProduct()
    {
        if (!$this->scannedProducts) {
           return $this->getProductFromDatabase();
        }

        if(!$this->searchProductFromExistingProducts())
        {
            return $this->getProductFromDatabase();
        }
    }
    
    private function getProductFromDatabase()
    {
        $productRepository = new ProductRepository();
        $product = $productRepository->getProductBySku($this->search, $this->user_id);
            
        if ($product) {

            $this->user_id = $product->user_id;
            $productArr = [];

            $productArr = [
                'id' => $product->id,
                'user_id' => $product->user_id,
                'user' => $product->user,
                'name' => $product->name,
                'description' => $product->description,
                'sku' => $product->sku,
                'sh_code' => $product->sh_code,
                'order' => $product->order,
                'price' => $product->price,
                'total_price' => $product->price,
                'quantity' => 1,
                'total_quantity' => $product->quantity,
                'weight' => ($product->measurement_unit == 'kg/cm') ? $product->weight : UnitsConverter::poundToKg($product->weight),
                'total_weight' => ($product->measurement_unit == 'kg/cm') ? $product->weight : UnitsConverter::poundToKg($product->weight),
            ];
            array_push($this->scannedProducts, $productArr);

            $this->search = '';
            $this->error = '';
            return true;
        }
            
        $this->productError = $productRepository->getError();
        $this->search = '';
        return false;
    }

    private function searchProductFromExistingProducts()
    {
        if (in_array($this->search, array_column($this->scannedProducts, 'sku'))) {
            $index = array_search($this->search, array_column($this->scannedProducts, 'sku'));
            if ($this->scannedProducts[$index]['quantity'] < $this->scannedProducts[$index]['total_quantity']) {
                $this->scannedProducts[$index]['quantity'] += 1;
                $this->scannedProducts[$index]['total_price'] = $this->scannedProducts[$index]['price'] * $this->scannedProducts[$index]['quantity'];
                $this->scannedProducts[$index]['total_weight'] += $this->scannedProducts[$index]['weight'];

                $this->search = '';
                $this->productError = '';
                return true;
            }

            $this->search = '';
            $this->productError = 'product is out of stock';
            return true;
        }

        return false;
    }

    public function totalProducts()
    {
        $this->totalProducts = count($this->scannedProducts);
    }
    

    public function placeOrder(ProductRepository $productRepository)
    {
        $this->validate([
            'scannedProducts' => 'required',
            'user_id' => 'required',
        ]);

        $order = $productRepository->placeInventoryOrder($this->createRequest());
        if ($order) {
            return redirect()->route('admin.parcels.edit', $order);
        }

        $this->productError = $productRepository->getError();
    }

    private function createRequest()
    {
        return new Request([
            'order_items' => $this->scannedProducts,
            'user_id' => $this->user_id,
        ]);
    }
    
}
