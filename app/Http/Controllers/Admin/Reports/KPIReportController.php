<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Models\Reports;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Services\Excel\Export\KPIReport;
use App\Repositories\Reports\KPIReportsRepository;
use Exception;
use Illuminate\Support\Facades\Route;

class KPIReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request, KPIReportsRepository $kpiReportsRepository)
    {
        $isScanKpi =$request->isScanKpi;
 
        $this->authorize('viewKPIReport',Reports::class);
        $trackings = [];
        $trackingCodeUsersName = [];
        $orderDates = [];
        if($request->start_date && $request->end_date || $request->trackingNumbers) {
            try{ 
            $response = $kpiReportsRepository->get($request);
            }
            catch(Exception $e){
                session()->flash('alert-danger', 'Error' . $e->getMessage());
                return back(); 
            }
            $trackings = $response['trackings'];
            $trackingCodeUsersName = $response['trackingCodeUsersName'];
            $orderDates = $response['orderDates'];
        }
        return view('admin.reports.kpi-report', compact('trackings','trackingCodeUsersName', 'orderDates','isScanKpi'));
    }
    public function store(Request $request)
    {
        if($request->order){
            
            $trackings = json_decode($request->order, true);
            $trackingCodeUsersName =json_decode($request->trackingCodeUsersName, true);
            $orderDates =json_decode($request->orderDates, true);
            
            $exportService = new KPIReport($trackings,$trackingCodeUsersName, $orderDates, $request->isScanKpi?'Aguardando pagamento':null);
            return $exportService->handle();
        }
    } 
   
}
