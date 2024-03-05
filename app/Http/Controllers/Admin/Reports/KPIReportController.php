<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Models\Reports;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Services\Excel\Export\KPIReport;
use App\Services\Excel\Export\AccrualReport;
use App\Repositories\Reports\KPIReportsRepository;
use Exception;
use App\Repositories\OrderRepository;
class KPIReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request, KPIReportsRepository $kpiReportsRepository)
    { 
        if($request->type=='accrual'){
            $this->authorize('viewTaxAndDutyReport',Reports::class);
            return view('admin.reports.report-accrual');
        }
        else{
            $this->authorize('viewKPIReport',Reports::class);
        }
            
        
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
        if($request->type == 'accrual'){
            if($request->start_date != null && $request->end_date != null)
                {
                    $start_date = $request->start_date.' 00:00:00';
                    $end_date = $request->end_date.' 23:59:59';
                    $orders = Order::whereBetween('order_date', [$start_date, $end_date])->get();
                }else{ 
                    $orders =  Order::all();
                }
                if(count($orders)<1){ 
                    session()->flash('alert-danger', 'No order found!');
                    return back(); 
                }
                 
            $exportService = new AccrualReport($orders);
            return $exportService->handle();
        }

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
