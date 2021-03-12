<?php

namespace App\Http\Livewire\Deposit;

use App\Repositories\DepositRepository;
use Livewire\Component;
use Livewire\WithPagination;

class TableComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pageSize = 50;
    
    public $user;
    public $uuid;
    public $last_four_digits;
    public $is_paid;
    public $trackingCode;
    public $warehouseNumber;
    public $type;

    public $sortBy = 'id';
    public $sortAsc = false;
    
    public function render()
    {
        return view('livewire.deposit.table-component',[
            'deposits' => $this->getDeposits()
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

    public function getDeposits()
    {
        return (new DepositRepository)->get(request()->merge([
            'user' => $this->user,
            'uuid' => $this->uuid,
            'last_four_digits' => $this->last_four_digits,
            'type' => $this->type,
            'is_paid' => $this->is_paid,
            'warehouseNumber' => $this->warehouseNumber,
            'trackingCode' => $this->trackingCode,
        ]),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
    }

    public function updating()
    {
        $this->resetPage();
    }
}
