<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AffiliateSale;
use App\Repositories\AffiliateSaleRepository;
use App\Services\Excel\Export\CommissionReport;
use App\Repositories\Reports\CommissionReportsRepository;
use App\Services\Excel\Export\SaleExport;
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
            if (auth()->user()->isAdmin()) {
                $users =$commissionReportsRepository->getCommissionReportOfUsers($request,false,0,$request->sort_by,$request->sort_order);
                $shipmentReport = new CommissionReport($users,$request);
                return $shipmentReport->handle();
            }
            else{
                $affiliateSaleRepository = new AffiliateSaleRepository();
                $sales = $affiliateSaleRepository->get($request, false);
                $exportService = new SaleExport($sales);
                return $exportService->handle();
            }
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
