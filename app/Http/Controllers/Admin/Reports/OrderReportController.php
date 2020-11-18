<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Reports\OrderReportsRepository;
use App\Services\Excel\Export\OrderExport;

class OrderReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index() 
    {
        return view('admin.reports.order-report');
    }
    
    public function create(OrderReportsRepository $orderReportsRepository) 
    {
        $orders =  $orderReportsRepository->getOrderReport();
        
        $ExportService = new OrderExport($orders);
        return $ExportService->handle();
    }
}
