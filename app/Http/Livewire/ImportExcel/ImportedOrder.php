<?php

namespace App\Http\Livewire\ImportExcel;

use Livewire\Component;
use Livewire\WithPagination;
use App\Repositories\ImportOrderRepository;

class ImportedOrder extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $pageSize = 50;
    public $date = '';
    public $name = '';
    public $client = '';
    public $tracking = '';
    public $reference = '';
    public $carrier = '';
    public $type = '';
    public $orderId = '';
    public $search;
   
    public $sortBy = 'id';
    public $sortDesc = true;

    public function mount($orders)
    {
        $this->order = $orders;
        $this->orderId = $this->order->id;
        $this->type = $this->type? $this->type : request('type');
    }

    public function render()
    {
        return view('livewire.import-excel.imported-order',[
            'importedOrders' => $this->getImportedOrder()
        ]);
    }

    public function getImportedOrder()
    {
        
        return (new ImportOrderRepository)->getImportedOrder(request()->merge([
            'date' => $this->date,
            'name' => $this->name,
            'client' => $this->client,
            'tracking' => $this->tracking,
            'reference' => $this->reference,
            'carrier' => $this->carrier,
            'type' => $this->type,
            'search' => $this->search,
        ]),true,$this->pageSize,$this->sortBy,$this->sortDesc ? 'DESC' : 'asc', $this->orderId);
    }
}
