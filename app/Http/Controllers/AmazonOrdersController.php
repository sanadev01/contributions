<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ImportedOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\AmazonSPClients\AuthApiClient;

class AmazonOrdersController extends Controller
{

    public function listOrders(Request $request)
    {        
        return view('admin.users.amazon.orders');
    }

    public function createOrder(Request $request)
    {
        $id = $request->id;
        $amazonOrder = DB::table('sale_orders AS so')
            ->join('sale_order_items AS soi', 'soi.sale_order_id', '=', 'so.id')
            ->join('amazon_products AS p', 'p.id', '=', 'soi.product_id')
            ->join('users AS u', 'u.id', '=', 'so.user_id')
            ->join('roles AS r', 'r.id', '=', 'u.role_id')
            ->select('so.*', 'soi.*', 'p.*', 'u.name as user_name', 'r.name as role_name')
            ->where('so.id', $id)
            ->get();
        // dd($amazonOrder);
        return view('admin.users.amazon.create-hd-order', compact('amazonOrder'));
    }
}
