<?php

namespace App\Http\Livewire\Deposit;
use Livewire\Component;
use Livewire\WithPagination;
use App\Repositories\DepositRepository;
use App\Services\Excel\Export\ExportLiabilityReport;
class TableLiability extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $pageSize = 50;    
    public $userName;
    public $poboxNumber;
    public $dateFrom;
    public $dateTo;
    public $sortBy = 'id';
    public $sortAsc = false;
    public $balance;
    public $userId; 
    // public $deposits; 
    
    protected $listeners = [
        'user:updated' => 'updateUser',
        'clear-search' => 'clearSearch',
    ];

    public function mount()
    {
        $this->dateFrom = '';
        $this->dateTo = '';
    }

    public function render()
    {
        return view('livewire.deposit.table-liability',[
            'deposits' => $this->getUserLiability()
        ]);
    }
    public function download()
    {
            $liabilityReport = new ExportLiabilityReport($this->getUserLiability());
            return $liabilityReport->handle();        
    }
    public function sortBy($name)
    {
        if ($name == $this->sortBy) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortBy = $name;
        }
    }

    public function getUserLiability()
    {
        return (new DepositRepository)->getUserLiability($this->getRequestData(),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
    }

    public function updateUser($userId)
    {
       $this->poboxNumber = $userId;
    }

    public function clearSearch()
    {
       $this->poboxNumber = null;
    }

    public function getRequestData()
    {
        return request()->merge([
            'user' => $this->userName,            
            'poboxNumber' => $this->poboxNumber,            
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'sortBy' => $this->sortBy,
            'sortOrder' => $this->sortAsc ? 'DESC' : 'DESC',
            'balance' => $this->balance,
        ]);
    }

    public function updating()
    {
        $this->resetPage();
    }
}
