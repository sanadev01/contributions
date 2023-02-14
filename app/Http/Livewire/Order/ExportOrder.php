<?php

namespace App\Http\Livewire\Order;

use Carbon\Carbon;
use App\Models\Reports;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExportOrder extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    private $query;

    public $pageSize = 50;
    public $sortAsc = false;
    public $sortBy = 'id';
    public $isComplete;

    public function render()
    {
        $date = date('Y-m-d').' 00:00:00';
        $this->isComplete = $this->getQuery()->where('is_complete', false)->where('created_at', ">=" ,$date)->first();
        return view('livewire.order.export-order', [
            'reports' => $this->getQuery()
            
        ]);

    }

    public function download(Reports $report)
    {
        if($report->path) {
            return response()->download($report->path);
        }
    }

    public function delete(Reports $report)
    {
        if($report->path) {
            Storage::delete(basename($report->path));
        }
        $report->delete();
    }

    public function getQuery()
    {
        $query = Reports::query();
        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }
        $report = $query->orderBy(
            $this->sortBy,
            $this->sortAsc ? 'ASC' : 'DESC'
        )->paginate($this->pageSize);
        return $report;
    }

    public function updating()
    {
        $this->resetPage();
    }
}
