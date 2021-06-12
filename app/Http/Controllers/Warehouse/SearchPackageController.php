<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchPackageController extends Controller
{
    public function __invoke()
    {
        return view('admin.warehouse.searchPackage.index');
    }
}
