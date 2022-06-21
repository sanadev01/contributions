<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Models\Reports;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Excel\Export\ShipmentReport;
use App\Repositories\Reports\OrderReportsRepository;
use App\Services\Excel\Export\ShipmentReportByMonth;

class ShipmentPerUserReportController extends Controller
{
    public function index(Request $request, OrderReportsRepository $orderReportsRepository)
    {
        $this->authorize('viewUserShipmentReport',Reports::class);
        
        if ( $request->dl ==1 ){
            $users = $orderReportsRepository->getShipmentReportOfUsers($request,false,0,$request->sort_by,$request->sort_order);
            $shipmentReport = new ShipmentReport($users, $request);
            return $shipmentReport->handle();
        }
        
        if ( $request->year ){
            $months = $orderReportsRepository->getShipmentReportOfUsersByMonth($request);
            $shipmentReport = new ShipmentReportByMonth($months,$request);
            return $shipmentReport->handle();
        }

        return view('admin.reports.shipment-report');
    }

    public function create(Request $request, OrderReportsRepository $orderReportsRepository)
    {
        $userOrders = $orderReportsRepository->getShipmentReportOfUsersByWeight($request->id, null, $request);
        return $userOrders;
    }
}
