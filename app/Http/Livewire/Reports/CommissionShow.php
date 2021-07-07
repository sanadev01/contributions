<?php

namespace App\Http\Livewire\Reports;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AffiliateSale;
use App\Repositories\AffiliateSaleRepository;

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
        
        return view('livewire.reports.commission-show',[
            'sales' => $this->getSales(),
        ]);
    }
    public function getSales()
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
        ]),true,$this->pageSize);
    }

    // public function getBalance()
    // {
    //     return AffiliateSale::has('user')->has('order')->get();
    // }

    // public function getSales($paginate = true)
    // {
    //     $request = request()->merge([
    //         'start' => $this->start,
    //         'end' => $this->end,
    //         'name' => $this->name,
    //         'order' => $this->order,
    //         'value' => $this->value,
    //         'user' => $this->user_commission,
    //         'whr' => $this->whr,
    //         'corrios_tracking' => $this->corrios_tracking,
    //         'reference' => $this->reference,
    //         'tracking' => $this->tracking,
    //         'weight' => $this->weight,
    //         'saleType' => $this->saleType,
    //         'commission' => $this->commission,
    //     ]);
    //     $query = AffiliateSale::has('user')->with('order')->has('order')->where('user_id', $this->user->id);
    //     // $query = $this->user->affiliateSales();
       

    //     if ( $request->start ){
    //         $startDate = $request->start . ' 00:00:00';
    //         $query->where(function($query) use($startDate){
    //             return $query->where('created_at','>',$startDate);
    //         });
    //     }
        
    //     if ( $request->end ){
    //         $endDate = $request->end.' 23:59:59';
    //         $query->where(function($query) use($endDate){
    //             return $query->where('created_at','<=', $endDate);
    //         });
    //     }

    //     if ( $request->name ){
            
    //         $query->whereHas('user',function($query) use($request) {
    //             return $query->where('name', 'LIKE', "%{$request->name}%");
    //         });
    //     }
    //     if ( $request->user ){
           
    //         $query->whereHas('order',function($query) use($request) {
    //              return $query->whereHas('user',function($query) use($request) {
    //                 return $query->where('name', 'LIKE', "%{$request->user}%");
    //             });
    //         });
    //     }

    //     if ( $request->order ){
    //         $query->where(function($query) use($request){
    //             return $query->where('order_id', 'LIKE', "%{$request->order}%");
    //         });

    //     }
    //     if ( $request->whr ){
    //         $query->whereHas('order',function($query) use($request){
    //             return $query->where('warehouse_number', 'LIKE', "%{$request->whr}%");
    //         });
    //     }
    //     if ( $request->corrios_tracking ){
    //         $query->whereHas('order',function($query) use($request){
    //             return $query->where('corrios_tracking_code', 'LIKE', "%{$request->corrios_tracking}%");
    //         });
    //     }
    //     if ( $request->reference ){
    //         $query->whereHas('order',function($query) use($request){
    //             return $query->where('customer_reference', 'LIKE', "%{$request->reference}%");
    //         });
    //     }

    //     if ( $request->tracking ){
    //         $query->whereHas('order',function($query) use($request){
    //             return $query->where('tracking_id', 'LIKE', "%{$request->tracking}%");
    //         });
    //     }
    //     if ( $request->weight ){
    //         $query->whereHas('order',function($query) use($request){
    //             return $query->where('weight', 'LIKE', "%{$request->weight}%");
    //         });
    //     }

    //     if ( $request->value ){
    //         $query->where(function($query) use($request){
    //             return $query->where('value',"{$request->value}");
    //         });
    //     }

    //     if ( $request->saleType ){
    //         $query->where(function($query) use($request){
    //             return $query->where('type',"$request->saleType");
    //         });
    //     }

    //     if ( $request->commission ){
    //         $query->where(function($query) use($request){
    //             return $query->where('commission', 'LIKE', "%{$request->commission}%");
    //         });
    //     }

    //     $sales = $query->orderBy('id','desc');
    //     return $paginate ? $sales->paginate($this->pageSize) : $sales->get();
        
    // }

    public function updating()
    {
        $this->resetPage();
    }
}
