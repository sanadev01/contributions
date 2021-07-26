<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Excel\Export\CommissionReport;
use App\Repositories\Reports\CommissionReportsRepository;

class CommissionReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request, CommissionReportsRepository $commissionReportsRepository) 
    {
        
        // $this->authorize('viewComissionReport',Reports::class);

        if ( $request->dl ==1 ){
            $users = $commissionReportsRepository->getCommissionReportOfUsers($request,false,0,$request->sort_by,$request->sort_order);
            $shipmentReport = new CommissionReport($users);
            return $shipmentReport->handle();
        }

        return view('admin.reports.commission-report');
    }
    
    public function show(User $commission) 
    {
        $commission->affiliateSales;
        $user = $commission;

        return view('admin.reports.commission-report-show', compact('user'));
    }
}
