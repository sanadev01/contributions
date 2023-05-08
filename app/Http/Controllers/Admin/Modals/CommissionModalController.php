<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AffiliateSale;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AffiliateSaleRepository;

class CommissionModalController extends Controller
{
    public function __invoke(Request $request)
    {
        if(!Auth::user()->isAdmin()){ 
            abort(401);
        }
        $sales =  (new AffiliateSaleRepository)->get(request()->merge([
            'status' => 'unpaid',
        ]), false);
        // $query = AffiliateSale::has('user')->with('order')->has('order');
        // $query->where('is_paid',false);
        // $sales = $query->groupBy('user_id')->get();
        $totalOrder = $sales->count();
        $totalCommission = number_format($sales->sum('commission'),2);
        
        $userSales = $sales->groupBy('user_id')->transform(function($item, $k) {
            return [
                'name' => $item->first()->user->name,
                'pobox_number' => $item->first()->user->pobox_number,
                'commission' =>  number_format($item->sum('commission'), 2),
                'orders' => $item->count(),
                'referrer' => $item->groupBy('referrer_id'),
            ];
        }); 
        return view('admin.modals.orders.commission', compact('totalCommission', 'totalOrder','sales','userSales'));
    }
}
