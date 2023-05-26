<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicApi\ArrivedOrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArrivedOrdersController extends Controller
{
    public function __invoke(Request $request)
    {
        $orders = Order::where('user_id', Auth::id())->where('arrived_date', '!=', null)
        ->when($request->from && $request->to, function ($query) use ($request) {
            $query->whereBetween('arrived_date', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
        })->when($request->to, function ($query) use ($request) {
            $query->whereDate('arrived_date',  '<=',$request->to . ' 23:59:59');
        })->when($request->from , function ($query) use ($request) { 
            $query->whereDate('arrived_date',  '>=',$request->from . ' 00:00:00');
        });
        $data['total'] = $orders->count();
        $itemsPerPage = request('item_per_page') ?? 50;
        $data['orders'] = ArrivedOrderResource::collection($orders
            ->when(
                $itemsPerPage != "all" && $itemsPerPage > 0,
                function ($query) use ($itemsPerPage,$request) {
                    return $query->offset($itemsPerPage * (($request->current_page ?? 1) - 1))
                        ->take($itemsPerPage);
                }
            )->orderBy('id', 'DESC')->get());

            return apiResponse(true,"Check in orders get successfully",$data);
    }
}
