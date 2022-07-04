<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Reports;
use App\Repositories\TrackingReportRepository;
use App\Services\Excel\Export\ExportOrderTrackings;
use Illuminate\Http\Request;

class TrackingReportController extends Controller
{
    public function index()
    {
        $this->authorize('downloadTrackingReport',Reports::class);
        return view('admin.reports.tracking-report');
    }

    public function store(Request $request, TrackingReportRepository $trackingReportRepository)
    {
        $this->authorize('downloadTrackingReport',Reports::class);
        $orders = $trackingReportRepository->get($request);

        $trackingExportService = new ExportOrderTrackings($orders);
        return $trackingExportService->handle();
    }
}
