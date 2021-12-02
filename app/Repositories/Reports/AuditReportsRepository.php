<?php

namespace App\Repositories\Reports;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse\AccrualRate;
use App\Services\Converters\UnitsConverter;

class AuditReportsRepository
{
    protected $error;

    public function get(Request $request,$paginate = true,$pageSize=50)
    {
        $query = Order::where('status','>=',Order::STATUS_PAYMENT_DONE)
        ->has('user');
        if ( $request->start_date) {
            $startDate = $request->start_date.' 00:00:00';
            $query->where('order_date','>=', $startDate);
        }
        if ($request->end_date ) {
            $endDate = $request->end_date.' 23:59:59';
            $query->where('order_date','<=', $endDate);
        }
        if ( $request->user_id){
            $query->where('user_id',$request->user_id);
        }
        $query->orderBy('id','desc');
        
        return $paginate ? $query->paginate($pageSize):$query->get();
    }

    public function getRates(Order $order)
    {
        $weight = $order->getWeight('kg');
        if($weight < 0.1){
            $weight = 0.1;
        }
        $weightToGrams = UnitsConverter::kgToGrams($weight);
        // $profitPackageRate = 0;
        // if($order->recipient->country_id != 250)
        // {
        //     $profitPackageRate = $order->shippingService->getRateFor($order,true,true);
        // }
        $serviceCode = optional($order->shippingService)->service_sub_class;
        $rateSlab = AccrualRate::where('service',$serviceCode)->where('weight','<=',$weightToGrams)->orderBy('id','DESC')->take(1)->first();
        if ( !$rateSlab ){
            return [
                'accrualRate' => 0,
                // 'profitPackageRate' => $profitPackageRate,
            ];
        }
        return [
            'accrualRate' => $rateSlab->gru,
            // 'profitPackageRate' => $profitPackageRate,
        ];
    }
}
