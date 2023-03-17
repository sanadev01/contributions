<?php

namespace App\Http\Livewire\Reports;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use App\Repositories\Reports\CommissionReportsRepository;

class CommissionReportTable extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pageSize = 50;
    
    public $name;
    public $pobox_number;
    public $email;
    public $start_date;
    public $end_date;
    public $search;

    public $sortBy = 'commission';
    public $sortAsc = false;
    public $years;
    public $year;
    
    public function render()
    {
        $startYear = 2020;
        $latestYear = date('Y'); 
        $this->years = range( $latestYear, $startYear );
        return view('livewire.reports.commission-report-table',[
            'users' => $this->getReportData(),
            'downloadLink' => route('admin.reports.commission.index',http_build_query(
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
        if (auth()->user()->isAdmin()) {
            return (new CommissionReportsRepository)->getCommissionReportOfUsers($this->getRequestData(),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
        }
        
        return (new CommissionReportsRepository)->getCommissionReportOfLoggedInUser($this->getRequestData(),true,$this->pageSize,$this->sortBy,$this->sortAsc ? 'asc' : 'desc');
    }

    public function getRequestData()
    {
        return request()->merge([
            'name' => $this->name,
            'pobox_number' => $this->pobox_number,
            'email' => $this->email,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'sort_by' => $this->sortBy,
            'search' => $this->search, 
            'sort_order' => $this->sortAsc ? 'asc' : 'desc',
            'year' => $this->year,
        ]);    
    }

    public function updating()
    {
        $this->resetPage();
    }
}
