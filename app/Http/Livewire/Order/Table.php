<?php

namespace App\Http\Livewire\Order;

use Livewire\Component;
use Livewire\WithPagination;
use App\Repositories\OrderRepository;

class Table extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;

    public $pageSize = 50;

    private $query;

    /**
     * Searchable Fields.
     */
    public $date = '';
    public $name = '';
    public $pobox = '';
    public $whr_number = '';
    public $merchant = '';
    public $carrier = '';
    public $tracking_id = '';
    public $customer_reference = '';
    public $tracking_code = '';
    public $amount = '';
    public $status = '';
    public $orderType = null;
    public $userType = null;
    public $paymentStatus = null;

    /**
     * Sort Asc.
     */
    public $sortAsc = false;
    public $sortBy = 'id';

    public function mount($userType = null)
    {
        $this->userType = $userType;
        $this->query = $this->getOrders();
    }

    public function render()
    {
        if (! $this->query) {
            $this->query = $this->getOrders();
        }

        return view('livewire.order.table', [
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
        return (new OrderRepository)->get(request()->merge([
            'order_date' => $this->date,
            'name' => trim($this->name),
            'pobox_number' => trim($this->pobox),
            'warehouse_number' => trim($this->whr_number),
            'merchant' => trim($this->merchant),
            'carrier' => trim($this->carrier),
            'gross_total' => trim($this->amount),
            'tracking_id' => trim($this->tracking_id),
            'customer_reference' => trim($this->customer_reference),
            'corrios_tracking_code' => trim($this->tracking_code),
            'status' => trim($this->status),
            'orderType' => trim($this->orderType),
            'paymentStatus' => trim($this->paymentStatus),
            'userType' => trim($this->userType),
        ]),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
    }

    public function updating()
    {
        $this->resetPage();
    }
}
