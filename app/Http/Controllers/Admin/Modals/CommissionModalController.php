<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\AffiliateSaleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $totalOrder = $sales->count();
        $totalCommission = number_format($sales->sum('commission'),2);      
        $userIds = $sales->pluck('user_id')->unique()->toArray();
        $userNames = User::whereIn('id', $userIds )->pluck('name'); 
        
        $userSales = $sales->groupBy('user_id')->transform(function($item, $k) {
            return [
                'name' => $item->first()->user->name,
                'pobox_number' => $item->first()->user->pobox_number,
                'commission' =>  number_format($item->sum('commission'), 2),
                'orders' => $item->count(),
                'referrer' => $item->groupBy('referrer_id'),
            ];
        }); 
        return view('admin.modals.orders.commission', compact('userNames', 'totalCommission', 'totalOrder','sales','userSales'));
    }
}
