<?php

namespace App\Http\Livewire\Label;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Excel\Export\ScanOrderExport;

class DriverReport extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
    private $orders;

    public $start_date;
    public $end_date;
    public $hasSearch = false;

    public function mount()
    {
        $this->orders = $this->getOrders();
    }

    public function render()
    {
        return view('livewire.label.driver-report', [
            'orders' => $this->getOrders(),
        ]);
    }

    public function search()
    {
        $validated = $this->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $this->start_date = $validated['start_date'].' 00:00:00';
        $this->end_date = $validated['end_date'].' 23:59:59';

        $this->hasSearch = true;
        return;
    }

    public function clearSearch()
    {
        if ($this->hasSearch) {
            $this->hasSearch = false;
        }
        $this->start_date = null;
        $this->end_date = null;

        return;
    }

    public function download()
    {
        $exportService = new ScanOrderExport($this->getOrders(false));
        return $exportService->handle();
    }

    private function getOrders($paginate = true)
    {
        $orders = Order::query();

        $orders->whereHas('trackings', function ($query) {
            $query->where('status_code', Order::STATUS_DRIVER_RECIEVED);

            $query->when($this->start_date, function ($query) {
                $query->where('created_at', '>=', $this->start_date);
            });
            
            $query->when($this->end_date, function ($query) {
                $query->where('created_at', '<=', $this->end_date);
            });
        });

       return ($paginate) ? $orders->latest()->paginate(25) : $orders->latest()->get();
    }
}
