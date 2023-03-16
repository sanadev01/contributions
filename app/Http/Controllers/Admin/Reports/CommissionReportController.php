<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AffiliateSale;
use App\Services\Excel\Export\CommissionReport;
use App\Repositories\Reports\CommissionReportsRepository;
use Illuminate\Support\Facades\Auth;

class CommissionReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request, CommissionReportsRepository $commissionReportsRepository) 
    {
        
        if ( $request->dl ==1 ){
            if(Auth::user()->isAdmin()){
                $users = $commissionReportsRepository->getCommissionReportOfUsers($request,false,0,$request->sort_by,$request->sort_order);
            }else{
                $users = $commissionReportsRepository->getCommissionReportOfLoggedInUser($request,false,0,$request->sort_by,$request->sort_order);
            }
            $shipmentReport = new CommissionReport($users,$request);
            dd($request->all());
            return $shipmentReport->handle();
        }

        return view('admin.reports.commission-report');
    }
    
    public function show(User $commission) 
    {
        if(Auth::user()->isAdmin()){
            $commission->affiliateSales;
            $user = $commission;
        }else{
            $user = $commission;
        }

        return view('admin.reports.commission-report-show', compact('user'));
    }
}
