<?php

namespace App\Http\Controllers\Admin\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesCommisionController extends Controller
{
    public function index()
    {
        return view('admin.affiliate.sales-commission');
    }
}
