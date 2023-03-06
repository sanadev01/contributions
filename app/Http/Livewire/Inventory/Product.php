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
    public $custom;
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
    public $quantity;
    public $expdate;

    public $sortBy = 'id';
    public $sortAsc = false;
    
    public function render()
    {
        return view('livewire.inventory.product',[
            'products' => $this->products(),
            'inventoryValue' => number_format($this->getSumOfProduct(),2) 
        ]);
    }

    public function sortBy($date)
    {
        if ($date == $this->sortBy) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortBy = $date;
        }
    }

    public function products()
    {
        $query = $this->getProduct()->orderBy($this->sortBy,$this->sortAsc ? 'asc' : 'desc');
        return $this->pageSize ? $query->paginate($this->pageSize) : $query->get();
    }
    public function getProduct()
    {
        // dd($this->custom);
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
            'expdate' => $this->expdate,
            'quantity' => $this->quantity,
            'search' => $this->search,
        ]),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
    
    }
    
    public function getSumOfProduct()
    {
        $query = $this->getProduct();
        $query->selectRaw('sum(price*quantity) as total');
        return $query->first()->total;
    }
    
    public function updating()
    {
        $this->resetPage();
    }
}
