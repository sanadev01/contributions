<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\Tax;
use App\Models\Deposit;
use App\Models\PaymentInvoice;
use Exception;

class TaxRepository
{
    public function get()
    {
        $taxlist = Tax::all();
        return $taxlist;
    }

    public function getOrders(Request $request)
    {
        $orders = null;

        $trackingNumber = explode(',', preg_replace('/\s+/', '', $request->trackingNumbers));
        if($trackingNumber){
            $orders = Order::where('user_id',$request->user_id)->whereIn('corrios_tracking_code', $trackingNumber)->get();
        }
        return $orders;
    }

    public function store(Request $request)
    {
        $data = [];
        $amount = 0;
        try{

            foreach($request->order_id as $key=> $orderId) {
                Tax::create([
                    'user_id' => $request->user_id,
                    'order_id' => $orderId,
                    'tax_1' => $request->tax_1[$key],
                    'tax_2' => $request->tax_2[$key],
                ]);
                $amount += $request->tax_2[$key];
            }
            $balance = Deposit::getCurrentBalance();
            if($balance > 0) {
                $charge = Deposit::create([
                    'uuid' => PaymentInvoice::generateUUID('DP-'),
                    'amount' => $amount,
                    'user_id' => $request->user_id,
                    'balance' => $balance - $amount,
                    'is_credit' => false,
                    'description' => "Pay Tax",
                ]);
            }
            else {
                session()->flash('alert-danger', 'Please recharge your account first!');
                return redirect()->route('admin.tax.create');
                }
            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Adding Tax'. $exception->getMessage());
            return null;
        }
    }

    public function update(Request $request)
    {

        //
    }


    public function delete()
    {
        //

    }

}
