<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Repositories\TrackingReportRepository;
use App\Services\Excel\Export\ExportOrderTrackings;
use Illuminate\Http\Request;

class TrackingReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.tracking-report');
    }

    public function store(Request $request, TrackingReportRepository $trackingReportRepository)
    {
        $orders = $trackingReportRepository->get($request);

        $trackingExportService = new ExportOrderTrackings($orders);
        return $trackingExportService->handle();
    }
}
