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
        
        if ( $request->dl ==1 ){
            $users = $orderReportsRepository->getShipmentReportOfUsers($request,false,0,$request->sort_by,$request->sort_order);
            $shipmentReport = new ShipmentReport($users);
            return $shipmentReport->handle();
        }
        
        if ( $request->year ){
            $users = $orderReportsRepository->getShipmentReportOfUsersByMonth($request);
            $shipmentReport = new ShipmentReport($users);
            return $shipmentReport->handle();
        }

        return view('admin.reports.shipment-report');
    }

    public function create(Request $request, OrderReportsRepository $orderReportsRepository)
    {
        $userOrders = $orderReportsRepository->getShipmentReportOfUsersByWeight($request->id);
        return $userOrders;
    }
}
