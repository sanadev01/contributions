<?php

namespace App\Http\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Product as ModelsProduct;
use App\Repositories\Inventory\ProductRepository;

class Product extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;

    public $pageSize = 50;

    private $query;

    public $date;
    public $name;
    public $user;
    public $price;
    public $status;
    public $sku;
    public $unit;
    public $weight;
    public $barcode;
    public $description;
    
    public function render()
    {
        return view('livewire.inventory.product',[
            'products' => $this->getProduct(),
            'inventoryValue' => number_format($this->getSumOfProduct(),2) 
        ]);
    }

    public function getProduct()
    {
        return (new ProductRepository)->get(request()->merge([
            'date' => $this->date,
            'user' => $this->user,
            'name' => $this->name,
            'price' => $this->price,
            'sku' => $this->sku,
            'unit' => $this->unit,
            'weight' => $this->weight,
            'barcode' => $this->barcode,
            'status' => $this->status,
            'description' => $this->description,
        ]),true,$this->pageSize);
    }
    
    public function getSumOfProduct()
    {
        $query = ModelsProduct::has('user');
        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }
        $query->selectRaw('sum(price) as total');
        return $query->first()->total;
    }
    
    public function updating()
    {
        $this->resetPage();
    }
}
