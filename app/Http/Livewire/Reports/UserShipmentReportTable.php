<?php

namespace App\Http\Livewire\Reports;

use App\Repositories\Reports\OrderReportsRepository;
use Livewire\Component;
use Livewire\WithPagination;

class UserShipmentReportTable extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pageSize = 50;
    
    public $user;
    public $start_date;
    public $end_date;

    public $sortBy = 'spent';
    public $sortAsc = false;
    
    public function render()
    {
        return view('livewire.reports.user-shipment-report-table',[
            'users' => $this->getReportData(),
            'downloadLink' => route('admin.reports.user-shipments.index',http_build_query(
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

    public function getReportData()
    {
        return (new OrderReportsRepository)->getShipmentReportOfUsers($this->getRequestData(),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
    }

    public function getRequestData()
    {
        return request()->merge([
            'user' => $this->user,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'sort_by' => $this->sortBy, 
            'sort_order' => $this->sortAsc ? 'asc' : 'desc'
        ]);    
    }

    public function updating()
    {
        $this->resetPage();
    }
}
