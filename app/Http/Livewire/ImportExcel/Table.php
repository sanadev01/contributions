<?php

namespace App\Http\Livewire\ImportExcel;

use Livewire\Component;
use Livewire\WithPagination;
use App\Repositories\ImportOrderRepository;

class Table extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $pageSize = 50;
    public $date = '';
    public $name = '';
    public $file_name = '';
    public $total = '';
   
    public $sortBy = 'id';
    public $sortDesc = true;

    public function render()
    {
        return view('livewire.import-excel.table',[
            'importOders' => $this->getImportOrder()
        ]);
    }

    public function getImportOrder()
    {
        return (new ImportOrderRepository)->get(request()->merge([
            'date' => $this->date,
            'name' => $this->name,
            'file_name' => $this->file_name,
            'total' => $this->total,
        ]),true,$this->pageSize,$this->sortBy,$this->sortDesc ? 'DESC' : 'asc');
    }
}
