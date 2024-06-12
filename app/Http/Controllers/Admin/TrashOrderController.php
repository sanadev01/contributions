<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\PreAlertRepository;

class TrashOrderController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewTrashedOrder',Order::class);
        return view('admin.orders.trash');
    }
    
    public function destroy($id,Request $request, PreAlertRepository $preAlertRepository)
    {
        try{

            $orderIds = array_map(function($id){
              return decrypt($id);  
            },json_decode($request->get('data'),true));
            
            Order::find($orderIds)->each->delete();

            session()->flash('alert-success','Parcel Deleted');
            return back();
             
        }catch(\Exception $e){
            session()->flash('alert-danger','Error While Deleting Parcel');
            return back();
        }
    }
}
