<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\User;
use App\Models\Reports;
use App\Jobs\ExportOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepository;
use App\Services\Excel\Export\OrderExport;

class OrderExportController extends Controller
{
    public function __invoke(Request $request)
    {

        $report = Reports::create([
            'name' => 'Orders Export',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        
        $report = $report->id;
        $request->merge(['report' => $report]);

        // dispatch(new ExportOrder(
        //     $request, Auth::user()
        // ))->onQueue('default');
        ExportOrder::dispatch($request->all(), Auth::user());
        return redirect()->back();
    }
}
