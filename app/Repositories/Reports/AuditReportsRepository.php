<?php

namespace App\Repositories\Reports;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ProfitPackage;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse\AccrualRate;
use App\Services\Converters\UnitsConverter;

class AuditReportsRepository
{
    protected $error;

    public function get(Request $request, $paginate = true, $pageSize = 50)
    {
        $query = Order::where('status', '>=', Order::STATUS_PAYMENT_DONE)
            ->has('user');
        if ($request->start_date) {
            $startDate = $request->start_date . ' 00:00:00';
            $query->where('order_date', '>=', $startDate);
        }
        if ($request->end_date) {
            $endDate = $request->end_date . ' 23:59:59';
            $query->where('order_date', '<=', $endDate);
        }
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        $query->orderBy('id', 'desc');

        return $paginate ? $query->paginate($pageSize) : $query->get();
    }

    public function getRates(Container $container, Order $order)
    {
        $weight = $order->getWeight('kg');
        if ($weight < 0.1) {
            $weight = 0.1;
        }
        $weightToGrams = UnitsConverter::kgToGrams($weight);
        $profitPackageRate = 0;
        $rate = 0;
        if ($order->recipient->country_id != 250) {
            $profitPackageRate = $order->shippingService->getRateFor($order, true, true);
            $rate = $order->shippingService->getRateFor($order, false, true);
        }
        $shippingServiceId = optional($order->shippingService)->id;
        $profitSetting = $order->user->profitSettings()->where('user_id', $order->user->id)->where('service_id', $shippingServiceId)->first();
        if ($profitSetting) {
            $profitPackage = $profitSetting->profitPackage;
        } else {
            $profitPackage = optional($order->user)->profitPackage;
        }

        if (!$profitPackage) {
            $profitPackage = ProfitPackage::where('type', ProfitPackage::TYPE_DEFAULT)->first();
        }
        $serviceCode = optional($order->shippingService)->service_sub_class;
        $rateSlab = AccrualRate::where('service', $serviceCode)->where('weight', '<=', $weightToGrams)->orderBy('id', 'DESC')->take(1)->first();
        if (!$rateSlab) {
            return [
                'accrualRate' => 0,
                'profitPackageRate' => $profitPackageRate,
                'rate' => $rate,
                'profitPackage' => $profitPackage->name,
            ];
        }
        $accuralRate = $rateSlab->cwb;
        if ($container->destination_ariport ==  "GRU") {
            $accuralRate = $rateSlab->gru;
        }
        return [
            'accrualRate' => $accuralRate,
            'profitPackageRate' => $profitPackageRate,
            'rate' => $rate,
            'profitPackage' => $profitPackage->name,
        ];
    }
}
