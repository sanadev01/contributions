<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Models\Reports;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Services\Excel\Export\KPIReport;
use App\Repositories\Reports\KPIReportsRepository;

class KPIReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request, KPIReportsRepository $kpiReportsRepository)
    {
        $this->authorize('viewKPIReport',Reports::class);
        $trackings = [];
        if($request->start_date && $request->end_date) {
            $trackings = $kpiReportsRepository->get($request);
        }
        return view('admin.reports.kpi-report', compact('trackings'));
    }

    public function store(Request $request, KPIReportsRepository $kpiReportsRepository)
    {
        if($request->order){
            $trackings = json_decode($request->order, true);
            $exportService = new KPIReport($trackings);
            return $exportService->handle();
        }
    }
}
