<?php

namespace App\Http\Livewire\PreAlert;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

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
    public $status = '';

    /**
     * Sort Asc.
     */
    public $sortAsc = false;
    public $sortBy = 'id';

    public function mount()
    {
        $this->query = $this->getQuery();
    }

    public function render()
    {
        if (! $this->query) {
            $this->query = $this->getQuery();
        }

        return view('livewire.pre-alert.table', [
            'parcels' => $this->query
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
        $this->query = $this->getQuery()->where('created_at', 'LIKE', "%{$this->date}%");
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
        $whrNumber = trim($this->whr_number);
        $this->query = $this->getQuery()->where('warehouse_number', 'LIKE', "%{$whrNumber}%");
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

    public function updatedStatus()
    {
        if ($this->status === 'transit') {
            $this->query = $this->getQuery()->where('is_shipment_added',false)->where('status','<>',Order::STATUS_CONSOLIDATOIN_REQUEST);
        }

        if ($this->status === 'ready') {
            $this->query = $this->getQuery()->where('is_shipment_added',true)->where('status','<>',Order::STATUS_CONSOLIDATOIN_REQUEST);
        }

        if ($this->status === '25') {
            $this->query = $this->getQuery()->where('status',$this->status);
        }
        if ($this->status === '26') {
            $this->query = $this->getQuery()->where('status',$this->status);
        }
    }

    public function getQuery()
    {
        $orders = Order::query()
            ->where('status','>',Order::STATUS_INVENTORY_FULFILLED)
            ->where('status','<',Order::STATUS_ORDER)
            ->has('user')
            ->doesntHave('parentOrder');
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id());
        }

        return $orders;
    }

    public function updating()
    {
        $this->resetPage();
    }

}
