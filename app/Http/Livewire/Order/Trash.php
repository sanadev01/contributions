<?php

namespace App\Http\Livewire\Order;

use Livewire\Component;
use Livewire\WithPagination;
use App\Repositories\OrderRepository;

class Trash extends Component
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


    public function mount()
    {
        $this->query = $this->getTrashedOrders();
    }

    public function render()
    {
        return view('livewire.order.table', [
            'orders' => $this->getTrashedOrders(),
        ]);
    }

    private function getTrashedOrders()
    {
        return (new OrderRepository)->get(request()->merge([
            'order_date' => $this->date,
            'name' => $this->name,
            'pobox_number' => $this->pobox,
            'warehouse_number' => $this->whr_number,
            'merchant' => $this->merchant,
            'carrier' => $this->carrier,
            'gross_total' => $this->amount,
            'tracking_id' => $this->tracking_id,
            'customer_reference' => $this->customer_reference,
            'corrios_tracking_code' => $this->tracking_code,
            'status' => $this->status,
            'orderType' => $this->orderType,
            'paymentStatus' => $this->paymentStatus,
            'userType' => $this->userType,
        ]),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc', true);
    }

    public function sortBy($name)
    {
        if ($name == $this->sortBy) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortBy = $name;
        }
    }
}
