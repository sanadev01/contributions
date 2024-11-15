<?php

namespace App\Http\Livewire\AmazonOrders;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Table extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    public $pageSize = 50;

    // Searchable Fields
    public $date = '';
    public $name = '';
    public $order_id = '';
    public $carrier = '';
    public $amount = '';
    public $items = '';
    public $status = '';

    // Sort
    public $sortAsc = false;
    public $sortBy = 'id';
    public $year = 2024;

    public function mount($userType = null)
    {
        $this->userType = $userType;
    }

    public function render()
    {
        return view('livewire.amazon-orders.table', [
            'orders' => $this->getOrders(),
            'isTrashed' => true
        ]);
    }

    public function sortBy($name)
    {
        if ($name == $this->sortBy) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortBy = $name;
        }
    }

    public function getOrders()
    {
        $query = DB::table('sale_orders AS so')
                ->join('sale_order_items AS soi', 'soi.sale_order_id', '=', 'so.id')
                ->join('amazon_products AS p', 'p.id', '=', 'soi.product_id')
                ->join('users AS u', 'u.id', '=', 'so.user_id')
                ->join('roles AS r', 'r.id', '=', 'u.role_id')
                ->select('so.*', 'soi.*', 'p.*', 'u.name as user_name', 'r.name as role_name');

        if (!Auth::user()->isAdmin()) {
            $query->where('so.user_id', Auth::user()->id);
        }

        if ($this->date) {
            $query->whereDate('so.purchase_date', 'like', '%' . $this->date . '%');
        }
        if ($this->name) {
            $query->where('u.name', 'like', '%' . $this->name . '%');
        }
        if ($this->order_id) {
            $query->where('so.amazon_order_id', 'like', '%' . $this->order_id . '%');
        }
        if ($this->carrier) {
            $query->where('so.shipment_service_level_category', 'like', '%' . $this->carrier . '%');
        }
        if ($this->amount) {
            $query->where('so.order_total', 'like', '%' . $this->amount . '%');
        }
        if ($this->items) {
            $query->where('so.number_of_items_shipped', 'like', '%' . $this->items . '%');
        }
        if ($this->status) {
            $query->where('so.order_status', $this->status);
        }

        $query->orderBy('so.' . $this->sortBy, $this->sortAsc ? 'asc' : 'desc');
        
        return $query->paginate($this->pageSize);
    }


    public function updating()
    {
        $this->resetPage();
    }
}
