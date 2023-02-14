<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\User;
use App\Models\Reports;
use App\Jobs\ExportOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepository;
use App\Services\Excel\Export\OrderExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

class OrderExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $startDate = $startDate ? $startDate : Carbon::now()->format('Y-m-d');
        $endDate = $endDate ? $endDate : Carbon::now()->format('Y-m-d');

        $report = Reports::create([
            'user_id' => Auth::id(),
            'name' => 'Orders Export',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        
        $request->merge(['report' => $report->id]);
        if($request->test=="true"){
            // $start = microtime(true); 

            $user = Auth::user(); 
            $orderRepository = new OrderRepository(); 
            $orders = $orderRepository->getOdersForExport($request, $user);

            $id = $user->id;
            $exportService = new OrderExport($orders, $id);
            $url = $exportService->handle(); 
            $end = microtime(true);
            // $time = $end - $start;
            // dd(gmdate('H:i:s', $time));
            return response()->download($url);
        
        }
        ExportOrder::dispatch($request->all(), Auth::user());
        return redirect()->route('admin.reports.export-orders');
    }
}
