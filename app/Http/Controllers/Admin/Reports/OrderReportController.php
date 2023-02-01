<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Reports;
use App\Repositories\Reports\OrderReportsRepository;
use App\Services\Excel\Export\OrderExport;
use Illuminate\Http\Request;

class OrderReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index() 
    {
        $this->authorize('viewOrderReport',Reports::class);
        return view('admin.reports.order-report');
    }
    
    public function create(OrderReportsRepository $orderReportsRepository) 
    {
        $this->authorize('viewOrderReport',Reports::class);
        $orders =  $orderReportsRepository->getOrderReport();
        
        $exportService = new OrderExport($orders);
        return $exportService->handle();
    }

    public function download()
    {
        return view('admin.reports.export-orders');
    }
}
