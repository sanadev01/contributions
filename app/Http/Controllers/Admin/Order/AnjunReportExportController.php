<?php

namespace App\Http\Controllers\Admin\Order;

use Carbon\Carbon;
use App\Models\Reports;
use Illuminate\Http\Request;
use App\Jobs\ExportAnjunReport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AnjunReportExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $todayDate = Carbon::now()->format('Y-m-d');
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $startDate = $startDate ? $startDate : $todayDate;
        $endDate = $endDate ? $endDate : $todayDate;

        $report = Reports::create([
            'user_id' => Auth::id(),
            'name' => 'Anjun Report',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        
        $request->merge(['report' => $report->id]);
        ExportAnjunReport::dispatch($request->all(), Auth::user());
        return redirect()->route('admin.reports.anjun-report');
    }
}
