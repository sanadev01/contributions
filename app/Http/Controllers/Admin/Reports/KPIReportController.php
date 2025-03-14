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
        $trackingCodeUsersName = [];
        $orderDates = [];
        $firstEventDate = [];
        if($request->start_date && $request->end_date || $request->trackingNumbers) {
            try{ 
            $response = $kpiReportsRepository->get($request);
            }
            catch(Exception $e){
                session()->flash('alert-danger', 'Error' . $e->getMessage());
                return back(); 
            }
            $trackings = $response['trackings'];
            $firstEventDate = $response['firstEventDate'];
            $trackingCodeUsersName = $response['trackingCodeUsersName'];
            $orderDates = $response['orderDates'];
        }
        return view('admin.reports.kpi-report', compact('trackings','trackingCodeUsersName', 'orderDates', 'firstEventDate'));
    }
    public function store(Request $request)
    {
        if($request->order){
            
            $trackings = json_decode($request->order, true);
            $trackingCodeUsersName =json_decode($request->trackingCodeUsersName, true);
            $orderDates =json_decode($request->orderDates, true);
            $firstEventDate =json_decode($request->firstEventDate, true);
            
            $exportService = new KPIReport($trackings,$trackingCodeUsersName, $orderDates, $request->type == 'scan' ?'Aguardando pagamento':null, $firstEventDate);
            return $exportService->handle();
        }
    } 
   
}
