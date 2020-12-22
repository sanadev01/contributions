<?php

namespace App\Http\Controllers\Admin\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateSale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(){

        return view('admin.affiliate.dashboard');
    }
}
