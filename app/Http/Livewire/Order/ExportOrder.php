<?php

namespace App\Http\Livewire\Order;

use App\Models\Reports;
use Livewire\Component;

class ExportOrder extends Component
{
    public function render()
    {
        $id = Reports::orderBy('id', 'desc')->value('id');
        $report = Reports::find($id);
        
        return view('livewire.order.export-order', compact('report'));
    }
}
