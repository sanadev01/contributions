<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Repositories\Reports\OrderReportsRepository;
use App\Services\Excel\Export\ShipmentReport;
use Illuminate\Http\Request;

class ShipmentPerUserReportController extends Controller
{
    public function __invoke(Request $request, OrderReportsRepository $orderReportsRepository)
    {
        if ( $request->dl ==1 ){
            $users = $orderReportsRepository->getShipmentReportOfUsers($request,false,0,$request->sort_by,$request->sort_order);
            $shipmentReport = new ShipmentReport($users);
            return $shipmentReport->handle();
        }

        return view('admin.reports.shipment-report');
    }
}
