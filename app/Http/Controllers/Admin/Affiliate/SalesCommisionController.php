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
    
    public function create(Request $request)
    {
        foreach(json_decode($request->data) as $saleId){
            $sale = AffiliateSale::find($saleId);
            $sale->is_paid = true;
            $sale->save();
        }
        session()->flash('alert-success','Commission has been paid');
        return redirect()->route('admin.affiliate.sales-commission.index');
    }
}
