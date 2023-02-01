<?php

namespace App\Http\Livewire\Order;

use App\Models\Reports;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class ExportOrder extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pageSize = 50;

    public function render()
    {
        $reports = Reports::all();
        return view('livewire.order.export-order', compact('reports'));
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

    public function updating()
    {
        $this->resetPage();
    }
}
