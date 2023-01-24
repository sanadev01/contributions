<?php

namespace App\Http\Livewire\Deposit;

use App\Repositories\DepositRepository;
use Carbon\Carbon;
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
    public $dateFrom;
    public $dateTo;
    public $attachment;
    public $sortBy = 'id';
    public $sortAsc = false;
    public $description;
    public $balance;
    public $userId;
    public $search;
    protected $listeners = [
        'user:updated' => 'updateUser',
        'clear-search' => 'clearSearch',
    ];

    public function mount()
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.deposit.table-component',[
            'deposits' => $this->getDeposits(),
            'downloadLink' => route('admin.deposit.index',http_build_query(
                $this->getRequestData()->all()
            )).'&dl=1'
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
        return (new DepositRepository)->get($this->getRequestData(),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
    }

    public function updateUser($userId)
    {
        $this->user = $userId;
    }

    public function clearSearch()
    {
        $this->user = null;
    }

    public function getRequestData()
    {
        return request()->merge([
            'user' => $this->user,
            'uuid' => $this->uuid,
            'last_four_digits' => $this->last_four_digits,
            'type' => $this->type,
            'is_paid' => $this->is_paid,
            'warehouseNumber' => $this->warehouseNumber,
            'trackingCode' => $this->trackingCode,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'sortBy' => $this->sortBy,
            'sortOrder' => $this->sortAsc ? 'Asc' : 'DESC',
            'attachment' => $this->attachment,
            'description' => $this->description,
            'balance' => $this->balance,
            'search'  => $this->search,
        ]);
    }

    public function updating()
    {
        $this->resetPage();
    }
}
