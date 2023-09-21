<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchPackageController extends Controller
{
    public function index()
    {
        return view('admin.warehouse.searchPackage.index');
    }
    
    public function show($id)
    {
        $order = Order::where('warehouse_number',$id)->first();
        return view('admin.warehouse.searchPackage.show', compact('order'));
    }
}
