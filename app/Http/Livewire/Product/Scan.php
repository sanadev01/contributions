<?php

namespace App\Http\Livewire\Product;

use App\Models\Product;
use Livewire\Component;

class Scan extends Component
{
    public $search;
    public $scannedProducts;
    public $productError;
    
    protected $rules = [
        'search' => 'required|min:4',
    ];

    public function render()
    {
        return view('livewire.product.scan');
    }

    public function updatedSearch()
    {
        $this->validate();
        $this->getProduct();
    }

    public function getProduct()
    {
        $product = Product::where([
            ['sku', $this->search],
            ['status', 'approved'],
            ['quantity', '>', 0],
        ])->first();

        if ($product) {
            $this->scannedProducts[] = $product;
            $this->search = '';
            $this->productError = '';

            return true;
        }

        $this->productError = 'Product not available';
    }

    public function placeOrder($id)
    {
        return redirect()->route('admin.inventory.product-order.show', $id);
    }
}
