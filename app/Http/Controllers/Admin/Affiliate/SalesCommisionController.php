<?php

namespace App\Http\Controllers\Admin\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateSale;
use Illuminate\Http\Request;

class SalesCommisionController extends Controller
{
    public function __construct()
    {
        
    }

    public function index()
    {
        $this->authorizeResource(AffiliateSale::class);

        return view('admin.affiliate.sales-commission');
    }
    
    public function create(Request $request)
    {
        $this->authorizeResource(AffiliateSale::class);
        
        AffiliateSale::whereIn('id',json_decode($request->data))->update([
            'is_paid' => true
        ]);
        session()->flash('alert-success','Commission has been paid');
        return redirect()->back();
    }
    
    public function destroy(AffiliateSale $sales_commission)
    {
        $sales_commission->delete();
        session()->flash('alert-success','Commission has been Deleted');
        return redirect()->back();
    }
}
