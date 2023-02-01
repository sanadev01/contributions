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

    private $query;

    public $pageSize = 50;
    public $sortAsc = false;
    public $sortBy = 'id';

    public function mount()
    {
        $this->query = $this->getQuery();
    }

    public function render()
    {
        if (! $this->query) {
            $this->query = $this->getQuery();
        }

        return view('livewire.order.export-order', [
            'reports' => $this->query
            ->orderBy(
                $this->sortBy,
                $this->sortAsc ? 'ASC' : 'DESC'
            )
            ->paginate($this->pageSize)
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
        return $query;
    }

    public function updating()
    {
        $this->resetPage();
    }
}
