<?php

namespace App\Http\Controllers\Admin\Order;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Reports;
use App\Jobs\ExportOrder;
use Illuminate\Http\Request;
use App\Jobs\ExportAnjunReport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepository;
use App\Services\Excel\Export\OrderExport;

class OrderExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $request['start_date']  = $request->start_date?? Carbon::now()->startOfMonth()->toDateString();
        $request['end_date']  = $request->end_date?? Carbon::now()->format('Y-m-d'); 
        $report = Reports::create([
            'user_id' => Auth::id(),
            'name' => $request->type == "anjun" ? "Anjun Report" : ( $request->type == "bcn" ? 'BCN Export':( $request->type == "correios" ? 'Correios Export':( $request->type == "anjun_china" ? 'Anjun China Export':"Orders Export"))),
            'start_date' => $request['start_date'],
            'end_date' => $request['end_date'],
        ]);
        $request->merge(['report' => $report->id]);
        if(in_array($request->type ,["anjun","anjun_china",'bcn','correios'])){
            ExportAnjunReport::dispatch($request->all(), Auth::user());
        }else {
            ExportOrder::dispatch($request->all(), Auth::user());
        }
        return redirect()->route('admin.reports.export-orders');
    }
}
