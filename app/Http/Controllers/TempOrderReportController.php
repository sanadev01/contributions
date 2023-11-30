<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Excel\Export\TempOrderExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TempOrderReportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $orders = Order::whereIn('corrios_tracking_code',[ 
            "NB878891236BR",
            "NB878783470BR",
            "NB878760540BR",
            "NB878784492BR",
            "NB878891474BR",
            "NB878783846BR",
            "NB878760482BR",
            "NB878784563BR",
            "NB878891369BR",
            "NB878783934BR",
            "NB878784444BR",
            "NB878784170BR",
            "NB878784532BR",
            "NB878891050BR",
            "NB878783982BR",
            "NB878891426BR",
            "NB878914997BR",
            "NB878915272BR",
        ])->get(); 
 
       $export =  new TempOrderExport($orders);
         $export->handle();
       return $export->download();
    }
}
