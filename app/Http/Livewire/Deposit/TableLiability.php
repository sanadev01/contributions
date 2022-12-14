<?php

namespace App\Http\Livewire\Deposit;

use Carbon\Carbon;
use App\Models\Deposit;
use Livewire\Component;
use Livewire\WithPagination;
use App\Repositories\DepositRepository;

class TableLiability extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pageSize = 50;
    
    public $user;
    public $poboxNumber;
    public $dateFrom;
    public $dateTo;
    public $sortBy = 'id';
    public $sortAsc = false;
    public $balance;
    public $userId; 
    
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
        $deposits = $this->getLiability();
        return view('livewire.deposit.table-liability',[
            'deposits' => $deposits,
            'totalBalance' => $this->getLiabilityBalance($deposits),
            'downloadLink' => route('admin.liability.index',http_build_query(
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

    public function getLiability()
    {

        return (new DepositRepository)->getLiability($this->getRequestData(),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
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

    public function getLiabilityBalance($deposits)
    {
         $sum = 0;
         foreach($deposits as $dep){
            $sum += getBalance($dep->user);
         }
         return $sum;
         
    }
    
    public function searchByBalance($query)
    { 
    }
}
