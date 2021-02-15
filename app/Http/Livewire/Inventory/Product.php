<?php

namespace App\Http\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
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
    public $sku;
    public $status;
    public $isStatus;
    public $description;

    public function mount($status)
    {
        $this->isStatus = $status;
    }
    
    public function render()
    {
        return view('livewire.inventory.product',[
            'products' => $this->getProduct() 
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
            'status' => $this->status,
            'description' => $this->description,
            'isStatus' => $this->isStatus,
        ]),true,$this->pageSize);
    }
    
    public function updating()
    {
        $this->resetPage();
    }
}
