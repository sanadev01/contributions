<?php

namespace App\Http\Livewire\Product;

use App\Models\Product;
use Livewire\Component;

class Scan extends Component
{
    public $search;
    public $scannedProducts;
    
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
        $this->scannedProducts = Product::where('sku', 'like', '%' . $this->search . '%')->get();
    }
}
