<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Reports\OrderReportsRepository;

class ShipmentByServiceController extends Controller
{
    public function __invoke(Request $request, User $user, OrderReportsRepository $orderReportsRepository)
    {
        $userOrderCount = $orderReportsRepository->orderReportByService($user,$request);
        return view('admin.modals.report.shipment-count',compact('userOrderCount'));
    }
}
