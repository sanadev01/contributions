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
   
    public $sortBy = 'id';
    public $sortDesc = true;

    
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
        ]),true,$this->pageSize,$this->sortBy,$this->sortDesc ? 'DESC' : 'asc');
    }
}
