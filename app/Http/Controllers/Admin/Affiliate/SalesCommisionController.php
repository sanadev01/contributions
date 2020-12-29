<?php

namespace App\Http\Controllers\Admin\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateSale;
use Illuminate\Http\Request;

class SalesCommisionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AffiliateSale::class);
    }

    public function index()
    {
        return view('admin.affiliate.sales-commission');
    }
}
