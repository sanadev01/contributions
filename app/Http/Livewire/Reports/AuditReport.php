<?php

namespace App\Http\Livewire\Reports;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Warehouse\AccrualRate;
use App\Services\Converters\UnitsConverter;
use App\Repositories\Reports\AuditReportsRepository;

class AuditReport extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;

    public $pageSize = 50;

    private $query;

    public $startDate;
    public $endDate;
    public $user;
    public $whr;
    
    public function render()
    {
        return view('livewire.reports.audit-report',[
            'auditRecords' => $this->getAuditRecord(),
        ]);
    }
    
    public function getAuditRecord()
    {
        return (new AuditReportsRepository)->get(request()->merge([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'user' => $this->user,
            'whr' => $this->whr,
        ]),true,$this->pageSize);
    }

    public function getRates(Order $order)
    {
        return (new AuditReportsRepository)->getRates($order);
    }
    
    public function updating()
    {
        $this->resetPage();
    }
}
