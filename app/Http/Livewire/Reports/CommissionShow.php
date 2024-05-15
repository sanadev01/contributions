<?php

namespace App\Http\Livewire\Reports;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AffiliateSale;
use App\Repositories\AffiliateSaleRepository;
use Illuminate\Support\Facades\Auth;

class CommissionShow extends Component
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
    public $user_commission;
    public $user;
    public $whr;
    public $corrios_tracking;
    public $reference;
    public $tracking;
    public $weight;
    public $value;
    public $saleType;
    public $commission;

    public function mount(User $user)
    { 
        $this->user = $user;
    }
    
    public function render()
    {
        if($this->user->id!=Auth::id()&&Auth::user()->isUser()){
            abort(404);
        }
        return view('livewire.reports.commission-show',[
            'sales' => $this->getSales(true),
            'saleReport' => $this->getSales(false),
        ]);
    }
    public function getSales($paginate)
    {
        return (new AffiliateSaleRepository)->get(request()->merge([
            'user_id' => $this->user->id,
            'start' => $this->start,
            'end' => $this->end,
            'name' => $this->name,
            'order' => $this->order,
            'value' => $this->value,
            'user' => $this->user_commission,
            'whr' => $this->whr,
            'corrios_tracking' => $this->corrios_tracking,
            'reference' => $this->reference,
            'tracking' => $this->tracking,
            'weight' => $this->weight,
            'saleType' => $this->saleType,
            'commission' => $this->commission,
        ]),$paginate,$this->pageSize);
    }

    public function updating()
    {
        $this->resetPage();
    }
}
