<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Reports;
use App\Services\Excel\Export\KPIReport;
use App\Repositories\Reports\KPIReportsRepository;
use Illuminate\Http\Request;

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

        $trackings = $kpiReportsRepository->get($request);
        if(empty($trackings)) {
            session()->flash('alert-danger', 'No Tracking Found');
            return back()->withInput();
        }
        return view('admin.reports.kpi-report', compact('trackings'));
    }

    public function create(Request $request, KPIReportsRepository $kpiReportsRepository)
    {
        $trackings = $kpiReportsRepository->getKPIReport($request);
        if(empty($trackings)) {
            session()->flash('alert-danger', 'No Order Found in the Selected Date Range');
            return back()->withInput();
        }
        $exportService = new KPIReport($trackings);
        return $exportService->handle();
    }
}
