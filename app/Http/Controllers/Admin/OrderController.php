<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\PreAlertRepository;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order, PreAlertRepository $preAlertRepository)
    {
        $this->authorize('delete',$order);

        if ( $preAlertRepository->delete($order) ){
            session()->flash('alert-success','Parcel Deleted');
            return back();
        }

        session()->flash('alert-danger','Error While Deleting Parcel');
        return back();
        
    }
}
