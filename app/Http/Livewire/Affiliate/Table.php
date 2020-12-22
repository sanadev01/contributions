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

    public $date;
    public $name;
    public $order;
    public $value;
    public $saleType;
    public $commission;

    public function render()
    {
        return view('livewire.affiliate.table',[
            'sales' => $this->getSales()
        ]);
    }

    public function getSales()
    {
        return (new AffiliateSaleRepository)->get(request()->merge([
            'date' => $this->date,
            'name' => $this->name,
            'order' => $this->order,
            'value' => $this->value,
            'saleType' => $this->saleType,
            'commission' => $this->commission,
        ]),true,$this->pageSize);
    }
    
    public function updating()
    {
        $this->resetPage();
    }
}
