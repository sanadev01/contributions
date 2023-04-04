<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use App\Models\AffiliateSale;
use App\Models\User;
use App\Repositories\AffiliateSaleRepository;
use Exception;
use Illuminate\Http\Request;

class CommissionModalController extends Controller
{
    public function __invoke(Request $request)
    {
        $end = $request->start;
        $start = $request->end;

        if ($request->orderIds) {
            $sales =  AffiliateSale::has('order')->has('user')->with(['order', 'user'])->whereIn('id', json_decode($request->orderIds))->where('is_paid', 'unpaid')->get();
        } else {
            $sales =  (new AffiliateSaleRepository)->get(request()->merge([
                'status' => 'unpaid',
            ]), false);
        }
        
        $totalOrder = $sales->count();
        $totalCommission = $sales->sum('value');
        $groupByUser = $sales->groupBy('user_id');

        return view('admin.modals.orders.commission', compact('start', 'end', 'groupByUser', 'totalCommission', 'totalOrder'));
    }
}
