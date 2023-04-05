<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\AffiliateSaleRepository;
use Illuminate\Http\Request;

class CommissionModalController extends Controller
{
    public function __invoke(Request $request)
    {
        $sales =  (new AffiliateSaleRepository)->get(request()->merge([
            'status' => 'unpaid',
        ]), false); 
        $totalOrder = $sales->count();
        $totalCommission = number_format($sales->sum('commission'),2);      
        $userIds = $sales->pluck('user_id')->unique()->toArray();
        $userNames = User::whereIn('id',$userIds)->pluck('name'); 
                 
        return view('admin.modals.orders.commission', compact('userNames', 'totalCommission', 'totalOrder','sales'));
    }
}
