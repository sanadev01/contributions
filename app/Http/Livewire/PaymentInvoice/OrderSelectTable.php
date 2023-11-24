<?php

namespace App\Http\Livewire\PaymentInvoice;

use Livewire\Component;
use Exception;
use App\Models\Order;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderSelectTable extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pageSize = 50;
    
    public $recipient;
    public $merchant;
    public $customer_reference;
    public $tracking_id;
    public $tracking_code;
    public $warehouse_number;
    public $sortBy = 'id';
    public $sortAsc = false;
    public $selectedOrder;

    public function mount()
    {
        $this->selectedOrder = request('order');
        
        if ($this->selectedOrder) {
            try {
                $this->selectedOrder = decrypt($this->selectedOrder);
            } catch (Exception $e) {
                $this->selectedOrder = null;
            }
        }
    }

    public function render()
    {

        return view('livewire.payment-invoice.order-select-table',[
            'orders' => $this->getUnpaidOrders(),
            'selected_order' => $this->selectedOrder
        ]);
    }

    public function getUnpaidOrders()
    {
        $query = Order::query()
                        ->where('user_id',Auth::id())
                        ->where('is_paid',false)
                        ->where('is_shipment_added',true)
                        ->where('status','>=',Order::STATUS_ORDER)
                        ->where('gross_total','>',0)
                        ->where('shipping_service_id','!=',null)
                        ->doesntHave('paymentInvoices')
                        ->with('recipient')
                        ->orderBy('id', 'desc');

        $query->when($this->recipient, function ($query) {
            $query->whereHas('recipient', function ($subquery) {
                $subquery->where('first_name', 'like', '%' . $this->recipient . '%')
                    ->orWhere('last_name', 'like', '%' . $this->recipient . '%');
            });
        });

        $query->when($this->merchant, function ($query) {
            $query->where('merchant', 'like', '%' . $this->merchant . '%');
        });

        $query->when($this->customer_reference, function ($query) {
            $query->where('customer_reference', 'like', '%' . $this->customer_reference . '%');
        });

        $query->when($this->tracking_id, function ($query) {
            $query->where('tracking_id', 'like', '%' . $this->tracking_id . '%');
        });

        $query->when($this->tracking_code, function ($query) {
            $query->where('corrios_tracking_code', 'like', '%' . $this->tracking_code . '%');
        });

        $query->when($this->warehouse_number, function ($query) {
            $query->where('warehouse_number', 'like', '%' . $this->warehouse_number . '%');
        });

        return $query->paginate($this->pageSize);
    }

    public function updating()
    {
        $this->resetPage();
    }

    public function toggleOrderSelection($orderId)
    {
        $this->selectedOrder = $this->selectedOrder == $orderId ? null : $orderId;
    }
}
