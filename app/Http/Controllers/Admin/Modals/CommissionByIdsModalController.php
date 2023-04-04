<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use App\Models\AffiliateSale;
use App\Models\User;
use App\Repositories\AffiliateSaleRepository;
use Exception;
use Illuminate\Http\Request;

class CommissionByIdsModalController extends Controller
{
    public function __invoke(Request $request)
    {  
        $sales =  (new AffiliateSaleRepository)->get(request()->merge([
            'status' => 'unpaid',
        ]), false);
        $totalOrder = $sales->count(); 
        $totalCommission = $sales->sum('value');
        $groupByUser = $sales->groupBy('user_id'); 
        return view('admin.modals.orders.commission-by-ids', compact('groupByUser', 'totalCommission', 'totalOrder'));
    }
}
