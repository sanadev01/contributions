<?php

namespace App\Http\Controllers\admin\reports;

use App\Models\Order;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Excel\Export\UnPaidOrdersReport;

class UnPaidOrdersController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {     
        $query = Order::with('user')->whereHas('user')->whereHas('paymentInvoices', function($query){
            return $query->where('last_four_digits', null);
        })->where('status' , '>=', Order::STATUS_PAYMENT_DONE);  

        $startDate  = $request->start_date.' 00:00:00';
        $endDate    = $request->end_date.' 23:59:59';
        if ( $request->start_date ){
            $query->where('order_date' , '>=',$startDate);
        }
        if ( $request->end_date ){
            $query->where('order_date' , '<=',$endDate);
        }

        $unPaidOrders = $query->whereDoesntHave('deposits')->orderBy('id','desc')->get();
        return view('admin.reports.unpaid-orders-report', compact('unPaidOrders'));
    }

    public function download(Request $request)
    {
        if($request->order){
            $unPaidTrackings = json_decode($request->order, true);
            $exportService = new UnPaidOrdersReport($unPaidTrackings);
            return $exportService->handle();
        }
    }
}
