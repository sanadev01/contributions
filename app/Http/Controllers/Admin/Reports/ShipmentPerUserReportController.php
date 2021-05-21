<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Reports;
use App\Repositories\Reports\OrderReportsRepository;
use App\Services\Excel\Export\ShipmentReport;
use Illuminate\Http\Request;

class ShipmentPerUserReportController extends Controller
{
    

    public function index(Request $request, OrderReportsRepository $orderReportsRepository)
    {
        $this->authorize('viewUserShipmentReport',Reports::class);
        
        $pageSize = 50;
        $sortBy = 'spent';
        $sortAsc = false;

        // for downloading records
        if ( $request->dl ==1 ){
            $users = $orderReportsRepository->getShipmentReportOfUsers($request,false,0,$request->sort_by,$request->sort_order);
            $shipmentReport = new ShipmentReport($users);
            return $shipmentReport->handle();
        }
        request()->merge([
            'sort_by' => $sortBy, 
            'sort_order' => $sortAsc ? 'asc' : 'desc'
        ]);    

        if ($request->exists('name') || $request->exists('pobox_number') || $request->exists('email')) 
        {
            request()->merge([
                'name' => $request->name,
                'pobox_number' => $request->pobox_number,
                'email' => $request->email,
            ]);    
        }
       
        // generating download link
        $downloadLink = route('admin.reports.user-shipments.index',http_build_query(
            $request->all()
        )).'&dl=1';
        
        
        // For displaying records
        $users = $orderReportsRepository->getShipmentReportOfUsers($request,true,$pageSize, $sortBy, $sortAsc ? 'asc' : 'desc');

        return view('admin.reports.shipment-report')->with([
            'users' => $users,
            'sortBy' => $sortBy,
            'sortAsc' => $sortAsc,
            'downloadLink' => $downloadLink

        ]);
    }

    public function create(Request $request, OrderReportsRepository $orderReportsRepository)
    {
        $userOrders = $orderReportsRepository->getShipmentReportOfUsersByWeight($request->id);
        return $userOrders;
    }
}
