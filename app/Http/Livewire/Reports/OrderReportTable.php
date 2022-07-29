<?php

namespace App\Http\Livewire\Reports;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class OrderReportTable extends Component
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
    public $status = '';
    public $orderType = null;
    public $paymentStatus = null;

    /**
     * Sort Asc.
     */
    public $sortAsc = false;
    public $sortBy = 'id';

    // public function render()
    // {
        
    //     return view('livewire.reports.order-report-table');
    // }
    public function mount()
    {
        $this->query = $this->getQuery();
    }

    public function render()
    {
        if (! $this->query) {
            $this->query = $this->getQuery();
        }

        return view('livewire.reports.order-report-table', [
            'orders' => $this->query
            ->orderBy(
                $this->sortBy,
                $this->sortAsc ? 'ASC' : 'DESC'
            )
            ->paginate($this->pageSize)
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

    public function updatedDate()
    {
        $this->query = $this->getQuery()->where('order_date', 'LIKE', "%{$this->date}%");
    }

    public function updatedName()
    {
        $this->query = $this->getQuery()->whereHas('user', function ($query) {
            return $query->where('name', 'LIKE', "%{$this->name}%");
        });
    }

    public function updatedPobox()
    {
        $this->query = $this->getQuery()->whereHas('user', function ($query) {
            return $query->where('pobox_number', 'LIKE', "%{$this->pobox}%");
        });
    }

    public function updatedWhrNumber()
    {
        if (strlen($this->whr_number) <= 0) {
            return $this->query = $this->getQuery();
        }

        $this->query = $this->getQuery()->where('warehouse_number', 'LIKE', "%{$this->whr_number}%");
    }

    public function updatedMerchant()
    {
        $this->query = $this->getQuery()->where('merchant', 'LIKE', "%{$this->merchant}%");
    }

    public function updatedCarrier()
    {
        $this->query = $this->getQuery()->where('carrier', 'LIKE', "%{$this->carrier}%");
    }

    
    public function updatedTrackingId()
    {
        $this->query = $this->getQuery()->where('tracking_id', 'LIKE', "%{$this->tracking_id}%");
    }

    public function updatedCustomerReference()
    {
        $this->query = $this->getQuery()->where('customer_reference', 'LIKE', "%{$this->customer_reference}%");
    }

    public function updatedTrackingCode()
    {
        $this->query = $this->getQuery()->where('corrios_tracking_code', 'LIKE', "%{$this->tracking_code}%");
    }

    public function updatedStatus()
    {
        if ($this->status !== '') {
            $this->query = $this->getQuery()->where('status',$this->status);
        }
    }

    public function updatedOrderType()
    {
        if ($this->orderType === 'consolidated') {
            $this->query = $this->getQuery()->where('is_consolidated',true);
        }

        if ($this->orderType === 'non-consolidated') {
            $this->query = $this->getQuery()->where('is_consolidated',false);
        }
    }

    public function updatedPaymentStatus()
    {
        if ($this->paymentStatus === 'paid') {
            $this->query = $this->getQuery()->where('is_paid',true);
        }

        if ($this->paymentStatus === 'unpaid') {
            $this->query = $this->getQuery()->where('is_paid',false);
        }
    }

    public function getQuery()
    {
        $orders = Order::query()
            ->where('status','>=',Order::STATUS_ORDER)
            ->has('user');
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id());
        }

        if($this->search)
        {
            $orders->where('tracking_id', 'LIKE', "%{$this->search}%")
            ->orWhere('warehouse_number', 'LIKE', "%{$this->search}%")
            ->orWhereHas('user', function ($orders) {
                return $orders->where('pobox_number', 'LIKE', "%{$this->search}%");
            })
            ->orWhereHas('user', function ($orders) {
                return $orders->where('name', 'LIKE', "%{$this->search}%");
            })
            ->orWhere('order_date', 'LIKE', "%{$this->search}%");
        }

        return $orders;
    }


}
