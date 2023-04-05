<?php

namespace App\Http\Livewire\Affiliate;

use App\Models\AffiliateSale;
use App\Repositories\AffiliateSaleRepository;
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

    public $start;
    public $end;
    public $name;
    public $order;
    public $user;
    public $whr;
    public $status;
    public $corrios_tracking;
    public $reference;
    public $tracking;
    public $weight;
    public $value;
    public $saleType;
    public $commission;
    public $customSearch;

    public function render()
    {
        return view('livewire.affiliate.table',[
            'sales' => $this->getSales(),
            'balance' => $this->getBalance()
        ]);
    }

    public function getSales()
    {
        return (new AffiliateSaleRepository)->get(request()->merge([
            'start' => $this->start,
            'end' => $this->end,
            'name' => $this->name,
            'order' => $this->order,
            'value' => $this->value,
            'user' => $this->user,
            'whr' => $this->whr,
            'status' => $this->status,
            'corrios_tracking' => $this->corrios_tracking,
            'reference' => $this->reference,
            'tracking' => $this->tracking,
            'weight' => $this->weight,
            'saleType' => $this->saleType,
            'commission' => $this->commission,
            'search' => $this->search,
        ]),true,$this->pageSize);
    }
    
    public function getBalance()
    {
        return AffiliateSale::has('user')->has('order')->get();
    }
    
    public function updating()
    {
        $this->resetPage();
    }
}
