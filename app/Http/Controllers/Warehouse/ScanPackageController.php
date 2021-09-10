<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScanPackageController extends Controller
{
    public function index()
    {
        return view('admin.print-label.create');
    }
}
