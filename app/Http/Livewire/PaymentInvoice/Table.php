<?php

namespace App\Http\Livewire\PaymentInvoice;

use App\Repositories\PaymentInvoiceRepository;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pageSize = 50;
    
    public $user;
    public $uuid;
    public $orderID;
    public $last_four_digits;
    public $type;
    public $is_paid;

    public $sortBy = 'id';
    public $sortAsc = false;
    
    public function render()
    {
        return view('livewire.payment-invoice.table',[
            'invoices' => $this->getInvoices()
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

    public function getInvoices()
    {
        return (new PaymentInvoiceRepository)->get(request()->merge([
            'user' => $this->user,
            'uuid' => $this->uuid,
            'orderID'=>$this->orderID,
            'last_four_digits' => $this->last_four_digits,
            'type' => $this->type,
            'is_paid' => $this->is_paid,
        ]),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
    }

    public function updating()
    {
        $this->resetPage();
    }
}
