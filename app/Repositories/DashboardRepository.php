<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\HandlingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{

    public function getDashboardStats($startDate = NULL, $endDate = NULL)
    {
        $carbon       = Carbon::now();
        $monthName    = $carbon->format('F');
        $currentYear  = $carbon->year;
        $currentmonth = $carbon->month;
        $today        = $carbon->format('Y-m-d');

        $currentYearsorders = Order::query();
        $currentMonthorders = Order::query();
        $CurentDay          = Order::query();
        $totalOrderQuery    = Order::query();
        if ($startDate && $endDate) {
            $date = [ $startDate . ' 00:00:00', $endDate . ' 23:59:59'];
        }
        else{
            $date = [$carbon->firstOfMonth().'', ($carbon->lastOfMonth()->addDays(1)).''];
        } 
            
            $currentYearsorders = $currentYearsorders->whereBetween('order_date', $date);
            $currentMonthorders = $currentMonthorders->whereBetween('order_date', $date);
            $CurentDay          = $CurentDay->whereBetween('order_date', $date);
            $totalOrderQuery    = $totalOrderQuery->whereBetween('order_date', $date);


        if ($startDate && $endDate) {
            $date = [$startDate . ' 00:00:00', $endDate . ' 23:59:59'];
            $currentYearsorders = $currentYearsorders->whereBetween('order_date', $date);
            $currentMonthorders = $currentMonthorders->whereBetween('order_date', $date);
            $CurentDay          = $CurentDay->whereBetween('order_date', $date);
            $totalOrderQuery    = $totalOrderQuery->whereBetween('order_date', $date);
        }
        if (Auth::user()->isUser()) {
            $authUser = Auth::id();
            $currentYearsorders->where('user_id', $authUser);
            $currentMonthorders->where('user_id', $authUser);
            $CurentDay->where('user_id', $authUser);
            $totalOrderQuery->where('user_id', $authUser);
        }

        $paymentDone = Order::STATUS_PAYMENT_DONE;
        $currentYearTotal    = $currentYearsorders->whereYear('order_date', $currentYear)->count();
        $currentYearConfirm  = $currentYearsorders->where('status', '>=', $paymentDone)->count();
        $currentmonthTotal   = $currentMonthorders->whereMonth('order_date', $currentmonth)->whereYear('order_date', $currentYear)->count();
        $currentmonthConfirm = $currentMonthorders->where('status', '>=', $paymentDone)->count();
        $currentDayTotal     = $CurentDay->whereDate('order_date', $today)->count();
        $currentDayConfirm   = $CurentDay->where('status', '>=', $paymentDone)->count();
        $totalOrder          = $totalOrderQuery->count();
        $totalCompleteOrders = $totalOrderQuery->where('status', '>=', $paymentDone)->count();

        //bar chart
        $lastDayOfCurrentMonth = Carbon::parse(Carbon::now())->endOfMonth();
        $lastTwelveMonths = Carbon::parse(Carbon::now())->endOfMonth()->subMonths(12);

        $totalOrderByMonth = Order::with('shippingService')->whereBetween('created_at', [$lastTwelveMonths, $lastDayOfCurrentMonth])->orderBy('created_at', 'asc')->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->format('Y-M');
            });
        $totalShippedOrder = Order::with('shippingService')->where('status', Order::STATUS_SHIPPED)->whereBetween('created_at', [$lastTwelveMonths, Carbon::now()])->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->format('Y-M');
            });
        $months = array_map(function ($key, $value) {
            return substr($key,5);
        }, array_keys($totalOrderByMonth->toArray()), $totalOrderByMonth->toArray());

        $totalOrderCount = array_map(function ($key, $value) {
            return count($value);
        }, array_keys($totalOrderByMonth->toArray()), $totalOrderByMonth->toArray());

        $totalShippedCount = array_map(function ($key, $value) {
            return count($value);
        }, array_keys($totalShippedOrder->toArray()), $totalShippedOrder->toArray());
        if (count($months) < 12) {
            $months[] = Carbon::parse(Carbon::now())->format('Y-M');
            $totalOrderCount[] = 0.001;
            $totalShippedCount[] = 0.001;
        }
        //bar  chart end

        // doughnut chart started
        $newValue = $currentYearTotal;
        $oldValue = Order::with('shippingService')->whereBetween('created_at', [Carbon::now()->subMonths(24), $lastTwelveMonths])->count();
        $percentIncreaseThisYear = number_format(((($newValue - $oldValue) / $oldValue) * 100), 2);

        $newValue = $totalOrderCount[count($totalOrderCount)-1];
        $oldValue = $totalOrderCount[count($totalOrderCount)-2];
        $percentIncreaseThisMonth = number_format(((($newValue - $oldValue) / $oldValue) * 100), 2);

        // chart

        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
        ->whereIn('status', [
            Order::STATUS_SHIPPED,
            Order::STATUS_PAYMENT_DONE,
            Order::STATUS_PAYMENT_PENDING,
            Order::STATUS_RELEASE,
            Order::STATUS_CANCEL,
            Order::STATUS_REFUND
        ])
        ->groupBy('status')
        ->get();

        $statusCounts = $statusCounts->pluck('count', 'status');

        // Now you can access the counts for each status like this:
        $shippedOrderCount = $statusCounts[Order::STATUS_SHIPPED] ?? 0;
        $doneOrderCount = $statusCounts[Order::STATUS_PAYMENT_DONE] ?? 0;
        $pendingOrderCount = $statusCounts[Order::STATUS_PAYMENT_PENDING] ?? 0;
        $releaseOrderCount = $statusCounts[Order::STATUS_RELEASE] ?? 0;
        $cancelledOrderCount = $statusCounts[Order::STATUS_CANCEL] ?? 0;
        $refundOrderCount = $statusCounts[Order::STATUS_REFUND] ?? 0;


        //doughnut chart end
        return  $order[] = [
            'totalOrders'         => $totalOrder,
            'totalCompleteOrders' => $totalCompleteOrders,
            'currentmonthTotal'   => $currentmonthTotal,
            'currentmonthConfirm' => $currentmonthConfirm,
            'currentDayTotal'     => $currentDayTotal,
            'currentDayConfirm'   => $currentDayConfirm,
            'currentYearTotal'    => $currentYearTotal,
            'currentYearConfirm'  => $currentYearConfirm,
            'monthName'           => $monthName,
            'totalShippedCount'   => $totalShippedCount,
            'totalOrderCount'     => $totalOrderCount,
            'months'              => $months,
            'percentIncreaseThisMonth' => $percentIncreaseThisMonth,
            'percentIncreaseThisYear'  => $percentIncreaseThisYear,
            'doughnutData' =>
            [
                ['x'=> "Shipped", 'value'=> $shippedOrderCount],
                ['x'=> "Done", 'value'=>  $doneOrderCount],
                ['x'=> "Pending", 'value'=>   $pendingOrderCount],
                ['x'=> "Release", 'value'=>   $releaseOrderCount],
                ['x'=> "Refund/Cancelled", 'value'=>    $cancelledOrderCount+$refundOrderCount]
            
 
            ]

        ];
    }
}
