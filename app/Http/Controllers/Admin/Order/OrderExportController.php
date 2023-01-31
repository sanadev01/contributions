<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\Reports;
use App\Events\OrderReport;
use App\Jobs\ExportOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use App\Services\Excel\Export\OrderExport;

class OrderExportController extends Controller
{
    public function __invoke(Request $request)
    {
        Reports::create([
            'name' => 'Orders Export',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        $report = Reports::all()->last()->value('id');
        $request->merge(['report' => $report]);

        ExportOrder::dispatch($request);
        // event(new OrderReport($request));
        
    }
}
