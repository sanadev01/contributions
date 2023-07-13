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

        $totalOrderByMonth = Order::whereBetween('created_at', [$lastTwelveMonths, $lastDayOfCurrentMonth])->orderBy('created_at', 'asc')->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->format('Y-M');
            });
        $totalShippedOrder = Order::where('status', Order::STATUS_SHIPPED)->whereBetween('created_at', [$lastTwelveMonths, Carbon::now()])->get()
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
        $oldValue = Order::whereBetween('created_at', [Carbon::now()->subMonths(24), $lastTwelveMonths])->count();
        $percentIncreaseThisYear = number_format(((($newValue - $oldValue) / $oldValue) * 100), 2);

        $newValue = $totalOrderCount[11];
        $oldValue = $totalOrderCount[10];
        $percentIncreaseThisMonth = number_format(((($newValue - $oldValue) / $oldValue) * 100), 2);

        // chart

        $shippedOrderCount = Order::where('status', Order::STATUS_SHIPPED)->count();
        $doneOrderCount = Order::where('status', Order::STATUS_PAYMENT_DONE)->count();
        $pendingOrderCount = Order::where('status', Order::STATUS_PAYMENT_PENDING)->count();
        $releaseOrderCount = Order::where('status', Order::STATUS_RELEASE)->count();
        $cancelledOrderCount = Order::where('status', Order::STATUS_CANCEL)->count();
        $refundOrderCount = Order::where('status', Order::STATUS_REFUND)->count();

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
            'doughnutData' => [
                $shippedOrderCount,
                $doneOrderCount,
                $pendingOrderCount,
                $releaseOrderCount,
                $cancelledOrderCount,
                $refundOrderCount,
            ]

        ];
    }
}
