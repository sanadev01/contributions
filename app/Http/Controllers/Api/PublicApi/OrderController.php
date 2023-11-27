<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller; 
use App\Http\Resources\PublicApi\OrderResource; 
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __invoke(Request $request)
    { 
                   
        $orderByColumn = $request->input('orderByColumn', 'created_at');
        $orderDirection = $request->input('orderDirection', 'desc');
        $orders = Order::where('user_id',Auth::id())->orderBy($orderByColumn, $orderDirection)->paginate($request->input('perPage', 10));
        return OrderResource::collection($orders);
    }
}
