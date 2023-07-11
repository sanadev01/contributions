<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Reports;
use App\Services\Excel\Export\AnjunReport;
use App\Repositories\Reports\AnjunReportsRepository;
use Illuminate\Http\Request;

class AnjunReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request, AnjunReportsRepository $anjunReportsRepository)
    {
        $this->authorize('viewAnjunReport',Reports::class);

        $orders = $anjunReportsRepository->get($request);

        return view('admin.reports.anjun-report', compact('orders'));
    }

    public function create(Request $request, AnjunReportsRepository $anjunReportsRepository)
    {

        $orders = $anjunReportsRepository->getAnjunReport($request);
        $exportService = new AnjunReport($orders);
        return $exportService->handle();
    }

    public function download()
    {
        return view('admin.reports.export-anjun-report');
    }
}
