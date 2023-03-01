<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Models\Reports;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Services\Excel\Export\KPIReport;
use App\Repositories\Reports\KPIReportsRepository;
use Exception;

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
        $trackingCodeUser = [];
        if($request->start_date && $request->end_date || $request->trackingNumbers) {
            try{ 
            $response = $kpiReportsRepository->get($request);
            }
            catch(Exception $e){
                session()->flash('alert-danger', 'Error' . $e->getMessage());
                return back(); 
            }
            $trackings = $response['trackings'];
            $trackingCodeUser = $response['trackingCodeUser'];
        }
        return view('admin.reports.kpi-report', compact('trackings','trackingCodeUser'));
    }

    public function store(Request $request)
    {
        if($request->order){
            $trackings = json_decode($request->order, true);
            $trackingCodeUser =json_decode($request->trackingCodeUser, true);

           
            $exportService = new KPIReport($trackings,$trackingCodeUser);
            return $exportService->handle();
        }
    }
}
